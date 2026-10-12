<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessStudentAnswerJob;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Dashboard Siswa: Menampilkan Ujian Aktif dan Riwayat Ujian yang telah selesai
     * Dilengkapi fitur filter mata pelajaran berdasarkan hari
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // PEMBATASAN KELAS SISWA:
        // Tampilkan ujian yang statusnya 'published' dan kelas siswa diizinkan mengakses ujian ini
        $activeExamsQuery = Exam::with(['subject', 'classroom', 'classrooms', 'teacher'])
            ->where('status', 'published')
            ->withCount('questions');

        if ($user->classroom_id) {
            $userClassId = (int)$user->classroom_id;
            $activeExamsQuery->where(function ($q) use ($userClassId) {
                $q->where('classroom_id', $userClassId)
                  ->orWhereHas('classrooms', function ($sq) use ($userClassId) {
                      $sq->where('classrooms.id', $userClassId);
                  });
            });
        } else {
            // Jika siswa belum memiliki kelas terdaftar di profilnya, jangan tampilkan ujian kelas lain
            $activeExamsQuery->whereRaw('1 = 0');
        }

        // FITUR FILTER BERDASARKAN HARI UNTUK SISWA
        if ($request->filled('day')) {
            $activeExamsQuery->where('day_of_week', $request->day);
        }

        // Filter Pencarian Judul/Mapel
        if ($request->filled('search')) {
            $search = trim($request->search);
            $activeExamsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // HIGH CONCURRENCY CACHE (500+ User): Cache query ujian aktif dashboard per kelas/filter
        $userClassId = (int)$user->classroom_id;
        $dayParam = $request->filled('day') ? $request->day : 'all';
        $searchHash = $request->filled('search') ? md5($request->search) : 'none';
        $cacheKey = "siswa_dashboard_exams_c{$userClassId}_d{$dayParam}_s{$searchHash}";

        $activeExams = Cache::remember($cacheKey, 30, function () use ($activeExamsQuery) {
            return $activeExamsQuery->latest()->get();
        });

        // Sesi ujian yang pernah diikuti siswa ini
        $mySessions = ExamSession::with(['exam.subject', 'exam.classroom', 'exam.teacher'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->keyBy('exam_id');

        $daysList = Exam::daysList();

        return view('siswa.dashboard', compact('activeExams', 'mySessions', 'daysList'));
    }

    /**
     * Masuk ke Ruang Ujian CBT
     * Memulai sesi pengerjaan, timer 1 jam (atau sesuai durasi ujian), dan memuat nomor soal
     */
    public function show(int $id): View|RedirectResponse
    {
        $user = Auth::user();
        $exam = Exam::with(['subject', 'classroom', 'teacher'])->findOrFail($id);

        // VALIDASI KELAS: Cegah akses jika kelas siswa tidak cocok dengan kelas sasaran ujian
        if (!$exam->allowsClassroom($user->classroom_id)) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akses ditolak! Ujian ini khusus untuk siswa ' . ($exam->classroom->name ?? 'kelas lain') . '.');
        }

        $hasOngoingSession = ExamSession::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'ongoing')
            ->exists();

        // CEK WAKTU MULAI UJIAN: Jika jam belum tiba dan belum memiliki sesi berjalan
        if ($exam->hasNotStartedYet() && !$hasOngoingSession) {
            return redirect()->route('siswa.ujian.token', $exam->id)
                ->with('error', 'Ujian belum dapat dimulai! Ujian dijadwalkan pada jam ' . $exam->formatted_time_range . ' (mulai pukul ' . substr($exam->start_time, 0, 5) . ' WIB).');
        }

        if (!empty($exam->token) && !$hasOngoingSession && !session()->get("exam_token_verified_{$exam->id}", false)) {
            return redirect()->route('siswa.ujian.token', $exam->id);
        }

        // Ambil atau buat sesi ujian baru untuk user yang login
        $session = ExamSession::firstOrCreate(
            [
                'user_id' => $user->id,
                'exam_id' => $exam->id,
            ],
            [
                'start_time' => Carbon::now(),
                'status' => 'ongoing',
                'score' => null,
            ]
        );

        // Jika ujian ini sudah pernah diselesaikan, arahkan langsung ke halaman hasil
        if ($session->isCompleted()) {
            return redirect()->route('siswa.ujian.hasil', $exam->id);
        }

        // Cek sisa waktu (hitung mundur dari start_time + duration)
        $remainingSeconds = $session->remaining_seconds;

        // Jika waktu sudah habis otomatis saat membuka, selesaikan ujian
        if ($remainingSeconds <= 0) {
            $this->finalizeExamSession($session, $exam);
            return redirect()->route('siswa.ujian.hasil', $exam->id)
                ->with('info', 'Waktu ujian telah berakhir dan jawaban Anda telah disimpan otomatis.');
        }

        // KEAMANAN ANTI-CHEAT & HIGH CONCURRENCY CACHE (500+ User):
        // Jangan sertakan kolom correct_answer ke browser siswa, dan cache butir soal publik di memory/Redis
        $questions = Cache::remember("exam_questions_public_{$exam->id}", 600, function () use ($exam) {
            $qs = Question::where('exam_id', $exam->id)
                ->orWhere(function ($query) use ($exam) {
                    $query->whereNull('exam_id')->where('subject_id', $exam->subject_id);
                })
                ->select(['id', 'exam_id', 'subject_id', 'question_text', 'image', 'option_a', 'option_b', 'option_c', 'option_d'])
                ->orderBy('id')
                ->get();

            $qs->makeHidden(['correct_answer']);
            return $qs;
        });

        if ($questions->isEmpty()) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Belum ada soal yang tersedia untuk ujian ini.');
        }

        // Ambil jawaban yang sudah pernah dijawab oleh siswa pada sesi ini
        $userAnswers = ExamAnswer::where('exam_session_id', $session->id)
            ->pluck('selected_answer', 'question_id')
            ->toArray();

        $isLocked = $session->isLocked();

        return view('siswa.exam.room', compact('exam', 'session', 'questions', 'userAnswers', 'remainingSeconds', 'isLocked'));
    }

    public function token(int $id): View|RedirectResponse
    {
        $user = Auth::user();
        $exam = Exam::with(['subject', 'classroom', 'teacher'])->withCount('questions')->findOrFail($id);

        // VALIDASI KELAS: Cegah akses jika kelas siswa tidak cocok dengan kelas ujian
        if (!$exam->allowsClassroom($user->classroom_id)) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akses ditolak! Ujian ini khusus untuk siswa ' . ($exam->classroom->name ?? 'kelas lain') . '.');
        }

        if ($exam->status !== 'published') {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Ujian ini sedang tidak aktif atau belum dibuka oleh Guru.');
        }
        return view('siswa.exam.token', compact('exam'));
    }

    public function verifyToken(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $exam = Exam::with('classroom')->findOrFail($id);

        // VALIDASI KELAS: Cegah akses jika kelas siswa tidak cocok dengan kelas ujian
        if (!$exam->allowsClassroom($user->classroom_id)) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Akses ditolak! Ujian ini khusus untuk siswa ' . ($exam->classroom->name ?? 'kelas lain') . '.');
        }

        $request->validate([
            'token' => [
                'required',
                'string',
                'size:7',
                'regex:/^[A-Za-z0-9]{7}$/',
            ],
        ], [
            'token.required' => 'Token ujian wajib diisi.',
            'token.size' => 'Token ujian harus tepat 7 karakter.',
            'token.regex' => 'Token hanya boleh menggunakan huruf dan angka.',
        ]);

        if ($exam->status !== 'published') {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Ujian ini sedang tidak aktif atau belum dibuka oleh Guru.');
        }

        // CEK WAKTU MULAI UJIAN:
        if ($exam->hasNotStartedYet()) {
            return redirect()->route('siswa.ujian.token', $exam->id)
                ->with('error', 'Ujian belum dapat dimulai! Ujian ini dijadwalkan pada jam ' . $exam->formatted_time_range . ' (mulai pukul ' . substr($exam->start_time, 0, 5) . ' WIB). Silakan tunggu hingga waktu ujian tiba.');
        }

        if ($exam->hasEnded()) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Waktu pelaksanaan ujian ini telah berakhir.');
        }

        if (!$exam->isValidToken($request->token)) {
            return back()
                ->withInput()
                ->withErrors([
                    'token' => 'Token ujian yang Anda masukkan tidak sesuai.',
                ]);
        }

        // Tandai bahwa siswa sudah berhasil melewati verifikasi token
        session()->put("exam_token_verified_{$exam->id}", true);

        return redirect()->route('siswa.ujian.show', $exam->id);
    }

    /**
     * Simpan jawaban realtime via AJAX saat siswa mengklik opsi A/B/C/D
     * Dilengkapi validasi keamanan otorisasi & masa berlaku waktu ujian
     */
    public function saveAnswer(Request $request, int $id): JsonResponse
    {
        $userId = Auth::id();
        $exam = Exam::findOrFail($id);

        $session = ExamSession::where('user_id', $userId)
            ->where('exam_id', $exam->id)
            ->where('status', 'ongoing')
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Sesi ujian tidak valid atau telah selesai.'], 403);
        }

        // KEAMANAN ANTI-CHEAT: Jika ujian terkunci (4x keluar tab), tolak penyimpanan jawaban!
        if ($session->isLocked()) {
            return response()->json([
                'error' => 'Ujian Anda telah TERKUNCI karena terdeteksi keluar dari layar ujian sebanyak 4 kali. Silakan hubungi Guru pembuat ujian untuk membuka akses.',
                'is_locked' => true,
            ], 403);
        }

        if ($session->remaining_seconds <= 0) {
            $this->finalizeExamSession($session, $exam);
            return response()->json(['error' => 'Waktu ujian telah habis.', 'timeout' => true], 403);
        }

        $request->validate([
            'question_id' => 'required|integer',
            'selected_answer' => 'required|in:A,B,C,D',
        ]);

        $questionId = (int)$request->question_id;

        // HIGH-CONCURRENCY CACHE (500+ User):
        // Validasi dan hitung kebenaran via in-memory cache/Redis tanpa query berulang ke database
        $questionsMap = Cache::remember("exam_questions_map_{$exam->id}", 600, function () use ($exam) {
            return Question::where('exam_id', $exam->id)
                ->orWhere(function ($query) use ($exam) {
                    $query->whereNull('exam_id')->where('subject_id', $exam->subject_id);
                })
                ->select(['id', 'exam_id', 'subject_id', 'correct_answer'])
                ->get()
                ->keyBy('id');
        });

        $question = $questionsMap->get($questionId);

        // Fallback jika butir soal baru saja ditambahkan dan belum ada di cache
        if (!$question) {
            $question = Question::find($questionId);
            if ($question && ($question->exam_id === $exam->id || ($question->exam_id === null && $question->subject_id === $exam->subject_id))) {
                Exam::clearExamCache($exam->id);
            } else {
                return response()->json(['error' => 'Soal tidak valid untuk ujian ini.'], 422);
            }
        }

        // Validasi integritas: pastikan soal terkait dengan ujian/mapel yang benar
        if ($question->exam_id && (int)$question->exam_id !== (int)$exam->id) {
            return response()->json(['error' => 'Soal tidak valid untuk ujian ini.'], 422);
        }

        // Kalkulasi kebenaran dilakukan di backend PHP, bukan di browser siswa
        $isCorrect = (strtoupper(trim((string)$question->correct_answer)) === strtoupper(trim((string)$request->selected_answer)));

        // Simpan jawaban siswa: jika Redis Queue aktif, serahkan langsung ke queue worker agar HTTP response super cepat (<5ms)
        if (config('queue.default') !== 'sync') {
            ProcessStudentAnswerJob::dispatch($session->id, $questionId, $request->selected_answer, $isCorrect);
        } else {
            // Fallback sync upsert instan
            ExamAnswer::upsert(
                [
                    [
                        'exam_session_id' => $session->id,
                        'question_id' => $questionId,
                        'selected_answer' => $request->selected_answer,
                        'is_correct' => $isCorrect,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ],
                ['exam_session_id', 'question_id'],
                ['selected_answer', 'is_correct', 'updated_at']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil disimpan.',
            'question_id' => $questionId,
            'selected_answer' => $request->selected_answer,
        ]);
    }

    /**
     * Catat Pelanggaran Integritas Ujian (Keluar Tab / Buka Aplikasi Lain / Keluar Fullscreen)
     * Jika mencapai 4 kali, sesi ujian otomatis dikunci (is_locked = true).
     */
    public function logViolation(Request $request, int $id): JsonResponse
    {
        $userId = Auth::id();
        $exam = Exam::findOrFail($id);

        $session = ExamSession::where('user_id', $userId)
            ->where('exam_id', $exam->id)
            ->where('status', 'ongoing')
            ->first();

        if (!$session) {
            return response()->json(['error' => 'Sesi ujian tidak valid atau telah selesai.'], 403);
        }

        $session->increment('violation_count');
        $session->last_violation_at = Carbon::now();

        // KETIKA MENCAPAI 4x PERCOBAAN KELUAR TAB / LAYAR: KUNCI UJIAN
        if ($session->violation_count >= 4) {
            $session->is_cheating_detected = true;
            $session->is_locked = true;
            $session->locked_at = Carbon::now();
        }

        $session->save();

        return response()->json([
            'success' => true,
            'message' => $session->is_locked 
                ? 'Ujian Anda telah TERKUNCI karena terdeteksi meninggalkan layar ujian sebanyak 4 kali. Hubungi guru pembuat ujian untuk membuka kunci.' 
                : 'Peringatan: Percobaan keluar dari layar ujian telah dicatat.',
            'violation_count' => $session->violation_count,
            'is_cheating_detected' => (bool)$session->is_cheating_detected,
            'is_locked' => (bool)$session->isLocked(),
        ]);
    }

    /**
     * Endpoint realtime cek status apakah kunci ujian sudah dibuka oleh Guru
     */
    public function checkLockStatus(int $id): JsonResponse
    {
        $userId = Auth::id();
        $session = ExamSession::where('user_id', $userId)
            ->where('exam_id', $id)
            ->first();

        if (!$session) {
            return response()->json(['is_locked' => false, 'status' => 'not_found']);
        }

        return response()->json([
            'success' => true,
            'is_locked' => $session->isLocked(),
            'violation_count' => $session->violation_count,
            'remaining_seconds' => $session->remaining_seconds,
            'is_completed' => $session->isCompleted(),
        ]);
    }

    /**
     * Selesaikan Ujian & Hitung Nilai Akhir
     */
    public function finish(Request $request, int $id): RedirectResponse
    {
        $userId = Auth::id();
        $exam = Exam::findOrFail($id);

        $session = ExamSession::where('user_id', $userId)
            ->where('exam_id', $exam->id)
            ->firstOrFail();

        if ($session->isCompleted()) {
            return redirect()->route('siswa.ujian.hasil', $exam->id);
        }

        // Jika sedang terkunci, tidak diizinkan menyelesaikan secara manual
        if ($session->isLocked()) {
            return redirect()->route('siswa.ujian.show', $exam->id)
                ->with('error', 'Ujian Anda sedang TERKUNCI karena terdeteksi keluar dari layar ujian sebanyak 4 kali. Hubungi guru pembuat ujian untuk membuka kunci.');
        }

        /*
        * Ambil soal yang sama dengan soal yang ditampilkan
        * pada halaman ruang ujian.
        */
        $questions = Question::where('exam_id', $exam->id)
            ->orWhere(function ($query) use ($exam) {
                $query->whereNull('exam_id')
                    ->where('subject_id', $exam->subject_id);
            })
            ->select(['id'])
            ->orderBy('id')
            ->get();

        /*
        * Jika tidak ada soal, jangan lanjutkan proses
        * penyelesaian ujian.
        */
        if ($questions->isEmpty()) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Belum ada soal yang tersedia untuk ujian ini.');
        }

        /*
        * Ambil ID soal yang memang termasuk dalam ujian ini.
        */
        $questionIds = $questions->pluck('id');

        /*
        * Hitung berapa soal yang sudah dijawab siswa.
        *
        * Hanya jawaban untuk soal yang benar-benar termasuk
        * dalam ujian ini yang dihitung.
        */
        $answeredCount = ExamAnswer::where('exam_session_id', $session->id)
            ->whereIn('question_id', $questionIds)
            ->whereNotNull('selected_answer')
            ->where('selected_answer', '!=', '')
            ->count();

        $totalQuestions = $questions->count();

        $unansweredCount = $totalQuestions - $answeredCount;


        /*
        * Cek apakah waktu ujian masih tersedia.
        *
        * Jika waktu masih ada, siswa wajib menjawab
        * seluruh soal sebelum dapat menyelesaikan ujian.
        *
        * Jika waktu sudah habis, ujian tetap boleh
        * difinalisasi secara otomatis.
        */
        $remainingSeconds = $session->remaining_seconds;

        if ($remainingSeconds > 0 && $unansweredCount > 0) {
            return redirect()
                ->route('siswa.ujian.show', $exam->id)
                ->with(
                    'error',
                    "Ujian belum dapat diselesaikan. Masih ada {$unansweredCount} soal yang belum dijawab."
                );
        }


        /*
        * Semua soal sudah dijawab atau waktu ujian
        * sudah habis.
        *
        * Lanjutkan proses perhitungan nilai.
        */
        $this->finalizeExamSession($session, $exam);

        return redirect()->route('siswa.ujian.hasil', $exam->id)
            ->with('success', 'Selamat! Anda telah berhasil menyelesaikan ujian.');
    }

    /**
     * Menampilkan Hasil Nilai Ujian
     * SYARAT KEAMANAN: Jika tidak selesai, maka nilai TIDAK KELUAR!
     */
    public function result(int $id): View|RedirectResponse
    {
        $userId = Auth::id();
        $exam = Exam::with(['subject', 'classroom'])->findOrFail($id);

        $session = ExamSession::where('user_id', $userId)
            ->where('exam_id', $exam->id)
            ->first();

        // Jika belum ada sesi atau status masih ongoing (belum selesai)
        if (!$session || !$session->isCompleted()) {
            return view('siswa.exam.result', [
                'exam' => $exam,
                'session' => $session,
                'isCompleted' => false,
                'score' => null, // Nilai tidak keluar sama sekali
                'correctAnswers' => 0,
                'wrongAnswers' => 0,
                'totalQuestions' => 0,
            ]);
        }

        $totalQuestions = (int) ($session->total_questions ?? $exam->questions()->count());
        $correctAnswers = (int) ($session->correct_answers ?? 0);
        $wrongAnswers = max(0, $totalQuestions - $correctAnswers);

        return view('siswa.exam.result', [
            'exam' => $exam,
            'session' => $session,
            'isCompleted' => true,
            'score' => $session->score,
            'correctAnswers' => $correctAnswers,
            'wrongAnswers' => $wrongAnswers,
            'totalQuestions' => $totalQuestions,
        ]);
    }

    /**
     * Helper kalkulasi skor & finalisasi sesi
     */
    private function finalizeExamSession(ExamSession $session, Exam $exam): void
    {
        $totalQuestions = Cache::remember("exam_questions_count_{$exam->id}", 600, function () use ($exam) {
            return Question::where('exam_id', $exam->id)
                ->orWhere(function ($query) use ($exam) {
                    $query->whereNull('exam_id')->where('subject_id', $exam->subject_id);
                })
                ->count();
        });

        $correctCount = ExamAnswer::where('exam_session_id', $session->id)
            ->where('is_correct', true)
            ->count();

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;

        $session->update([
            'status' => 'completed',
            'end_time' => Carbon::now(),
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctCount,
        ]);
    }
}
