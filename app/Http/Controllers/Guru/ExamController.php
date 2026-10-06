<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use App\Services\DocxQuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExamController extends Controller
{
    /**
     * Tampilkan daftar seluruh ujian yang relevan dengan mata pelajaran guru
     * Dilengkapi fitur filter kelas, filter hari, pencarian, dan status
     */
    public function index(Request $request): View
    {
        $guru = auth()->user();
        $query = Exam::with(['subject', 'classroom', 'classrooms', 'teacher'])
            ->withCount(['questions', 'sessions']);

        // Jika guru memiliki mata pelajaran yang diampu, hanya tampilkan ujian mapel tersebut
        if ($guru->subjects()->exists()) {
            $subjectIds = $guru->subjects->pluck('id')->toArray();
            $query->where(function ($q) use ($guru, $subjectIds) {
                $q->where('user_id', $guru->id)
                  ->orWhereIn('subject_id', $subjectIds);
            });
        }

        // FITUR FILTER KELAS UNTUK GURU
        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        // FITUR FILTER BERDASARKAN HARI (Mata Pelajaran / Jadwal Ujian)
        if ($request->filled('day')) {
            $query->where('day_of_week', $request->day);
        }

        // Filter Status (draft / published)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Search (Judul / Mapel)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $exams = $query->latest()->paginate(10)->withQueryString();

        // Data untuk dropdown filter
        $classrooms = Classroom::orderBy('name')->get();
        $daysList = Exam::daysList();

        return view('guru.exam.index', compact('exams', 'classrooms', 'daysList'));
    }

    /**
     * Tampilkan form pembuatan ujian baru (Hanya pilihan Mapel yang diampu Guru)
     */
    public function create(): View
    {
        $guru = auth()->user();

        // PEMBATASAN MATA PELAJARAN:
        // Guru hanya bisa memilih mata pelajaran yang telah ditugaskan kepadanya
        if ($guru->subjects()->exists()) {
            $subjects = $guru->subjects()->orderBy('name')->get();
        } else {
            $subjects = Subject::orderBy('name')->get();
        }

        $classrooms = Classroom::orderBy('name')->get();
        $daysList = Exam::daysList();

        return view('guru.exam.create', compact('subjects', 'classrooms', 'daysList'));
    }

    /**
     * Simpan ujian baru
     */
    public function store(Request $request): RedirectResponse
    {
        $guru = auth()->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'classroom_ids' => 'nullable|array',
            'classroom_ids.*' => 'exists:classrooms,id',
            'duration' => 'nullable|integer|min:1|max:600',
            'status' => 'required|in:draft,published',
            'day_of_week' => 'nullable|string',
            'exam_date' => 'nullable|date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'token' => [
                'nullable',
                'string',
                'max:20',
            ],
        ]);

        // Pastikan setidaknya satu kelas sasaran dipilih
        $classroomIds = $request->input('classroom_ids', []);
        if (empty($classroomIds) && $request->filled('classroom_id')) {
            $classroomIds = [(int)$request->classroom_id];
        }

        if (empty($classroomIds)) {
            return back()->withInput()->with('error', 'Silakan pilih setidaknya satu kelas sasaran ujian.');
        }

        // VALIDASI KEAMANAN OTORISASI:
        // Pastikan guru tidak bisa memilih mata pelajaran di luar yang diampunya
        if ($guru->subjects()->exists()) {
            $allowedSubjectIds = $guru->subjects->pluck('id')->toArray();
            if (!in_array((int)$request->subject_id, $allowedSubjectIds)) {
                return back()->withInput()->with('error', 'Akses ditolak! Anda hanya berwenang membuat ujian pada mata pelajaran yang Anda ampu.');
            }
        }

        $token = $request->filled('token') ? strtoupper(trim($request->token)) : Exam::generateToken();

        // Hitung durasi otomatis dari start_time dan end_time jika tidak diisi manual
        $duration = $request->filled('duration') ? (int)$request->duration : null;
        if (empty($duration) && $request->filled('start_time') && $request->filled('end_time')) {
            try {
                $start = \Carbon\Carbon::parse(substr($request->start_time, 0, 5));
                $end = \Carbon\Carbon::parse(substr($request->end_time, 0, 5));
                $diff = $start->diffInMinutes($end, false);
                if ($diff > 0) {
                    $duration = (int) $diff;
                }
            } catch (\Exception $e) {
                // Ignore parse errors
            }
        }
        if (empty($duration)) {
            $duration = 60; // Standar fallback
        }

        // Otomatis deteksi hari dalam Bahasa Indonesia jika tanggal diisi
        $dayOfWeek = null;
        if ($request->filled('exam_date')) {
            $carbonDate = \Carbon\Carbon::parse($request->exam_date);
            $dayOfWeek = match ($carbonDate->dayOfWeekIso) {
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
                7 => 'Minggu',
                default => null,
            };
        } elseif ($request->filled('day_of_week')) {
            $dayOfWeek = $request->day_of_week;
        }

        $exam = Exam::create([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'classroom_id' => $classroomIds[0],
            'user_id' => $guru->id,
            'duration' => $duration,
            'status' => $request->status,
            'day_of_week' => $dayOfWeek,
            'exam_date' => $request->exam_date,
            'start_time' => $request->filled('start_time') ? $request->start_time : null,
            'end_time' => $request->filled('end_time') ? $request->end_time : null,
            'token' => $token,
        ]);

        // Hubungkan semua kelas sasaran ke tabel pivot
        $exam->classrooms()->sync($classroomIds);

        return redirect()->route('guru.ujian.show', $exam->id)
            ->with('success', "Ujian berhasil dibuat dengan Token [ {$token} ]! Silakan tambahkan butir soal.");
    }

    /**
     * Tampilkan detail ujian & bank soal ujian tersebut
     */
    public function show(int $id): View
    {
        $exam = Exam::with(['subject', 'classroom', 'classrooms', 'questions'])->findOrFail($id);

        return view('guru.exam.show', compact('exam'));
    }

    /**
     * Mulai Ujian / Buka Akses Ujian untuk Siswa (Toggle Status)
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $exam = Exam::findOrFail($id);

        if ($exam->status === 'published') {
            $exam->status = 'draft';
            $message = 'Ujian ditutup (status diubah ke Draft). Siswa tidak dapat mengakses ujian ini.';
        } else {
            if ($exam->questions()->count() === 0) {
                return back()->with('error', 'Tidak dapat memulai ujian! Belum ada soal yang diinput.');
            }
            $exam->status = 'published';
            $message = 'Ujian berhasil DIMULAI dan DIBUKA! Siswa sekarang dapat mengerjakan ujian.';

            
        }

        $exam->save();

        return back()->with('success', $message);
    }

    /**
     * Tampilkan form input soal (teks atau gambar) untuk ujian
     */
    public function questionCreate(int $examId): View
    {
        $exam = Exam::with(['subject', 'classroom'])->findOrFail($examId);

        return view('guru.question.create', compact('exam'));
    }

    /**
     * Simpan soal baru dengan teks atau upload gambar dan kunci jawaban
     */
    public function questionStore(Request $request, int $examId): RedirectResponse
    {
        $exam = Exam::findOrFail($examId);

        $request->validate([
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $exam->subject_id,
            'question_text' => $request->question_text,
            'image' => $imagePath,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => $request->correct_answer,
        ]);

        return redirect()->route('guru.ujian.show', $exam->id)
            ->with('success', 'Soal baru berhasil ditambahkan ke ujian!');
    }

    /**
     * Unduh template dokumen Word (.docx) untuk pengisian soal secara massal
     */
    public function downloadDocxTemplate(DocxQuestionService $docxService): BinaryFileResponse
    {
        $tempPath = $docxService->createTemplateFile();

        return response()->download($tempPath, 'template_soal_cbt_mtsn12.docx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Impor soal secara massal dari file template dokumen Word (.docx)
     */
    public function questionImportDocx(Request $request, int $examId, DocxQuestionService $docxService): RedirectResponse
    {
        $guru = auth()->user();
        $exam = Exam::findOrFail($examId);

        // Validasi Otorisasi: Pastikan guru berhak mengelola ujian ini
        if ($guru->subjects()->exists()) {
            $allowedSubjectIds = $guru->subjects->pluck('id')->toArray();
            if ($exam->user_id !== $guru->id && !in_array((int)$exam->subject_id, $allowedSubjectIds)) {
                return redirect()->route('guru.ujian.show', $examId)
                    ->with('error', 'Akses ditolak! Anda tidak memiliki izin untuk mengimpor soal pada ujian mata pelajaran ini.');
            }
        }

        $request->validate([
            'docx_file' => 'required|file|mimes:docx|max:15360', // max 15MB
        ], [
            'docx_file.required' => 'Silakan pilih file dokumen Word (.docx) yang akan diimpor.',
            'docx_file.mimes' => 'Format file harus dokumen Microsoft Word (.docx).',
            'docx_file.max' => 'Ukuran file dokumen Word tidak boleh melebihi 15MB.',
        ]);

        $result = $docxService->importFromDocx($request->file('docx_file'), $exam);

        if (!$result['success']) {
            return redirect()->route('guru.ujian.show', $examId)
                ->with('error', $result['message'])
                ->with('import_errors', $result['errors'] ?? []);
        }

        $flashMessage = $result['message'];
        if (!empty($result['errors'])) {
            $flashMessage .= ' (Catatan: ' . count($result['errors']) . ' baris dilewati karena format tidak lengkap).';
        }

        return redirect()->route('guru.ujian.show', $examId)
            ->with('success', $flashMessage)
            ->with('import_warnings', $result['errors'] ?? []);
    }

    /**
     * Hapus soal

     */
    public function questionDestroy(int $id): RedirectResponse
    {
        $question = Question::findOrFail($id);
        $examId = $question->exam_id;

        if ($question->image && Storage::disk('public')->exists($question->image)) {
            Storage::disk('public')->delete($question->image);
        }

        $question->delete();

        return redirect()->route('guru.ujian.show', $examId)
            ->with('success', 'Soal berhasil dihapus.');
    }

    /**
     * Hapus ujian
     */
    public function destroy(int $id): RedirectResponse
    {
        $exam = Exam::findOrFail($id);

        // Hapus file gambar soal terkait jika ada
        foreach ($exam->questions as $q) {
            if ($q->image && Storage::disk('public')->exists($q->image)) {
                Storage::disk('public')->delete($q->image);
            }
        }

        $exam->delete();

        return redirect()->route('guru.ujian.index')
            ->with('success', 'Ujian berhasil dihapus.');
    }

    /**
     * Tampilkan form edit soal
     */
    public function questionEdit(int $id): View
    {
        $guru = auth()->user();
        $question = Question::with(['exam.subject', 'exam.classroom'])->findOrFail($id);
        $exam = $question->exam;

        // Validasi Otorisasi Guru
        if ($exam && $guru->subjects()->exists()) {
            $allowedSubjectIds = $guru->subjects->pluck('id')->toArray();
            if ($exam->user_id !== $guru->id && !in_array((int)$exam->subject_id, $allowedSubjectIds)) {
                abort(403, 'Akses ditolak! Anda tidak berwenang mengedit soal ini.');
            }
        }

        return view('guru.question.edit', compact('question', 'exam'));
    }

    /**
     * Simpan pembaruan butir soal (teks soal, gambar, pilihan A-D, dan kunci jawaban)
     */
    public function questionUpdate(Request $request, int $id): RedirectResponse
    {
        $guru = auth()->user();
        $question = Question::findOrFail($id);
        $exam = $question->exam;

        // Validasi Otorisasi Guru
        if ($exam && $guru->subjects()->exists()) {
            $allowedSubjectIds = $guru->subjects->pluck('id')->toArray();
            if ($exam->user_id !== $guru->id && !in_array((int)$exam->subject_id, $allowedSubjectIds)) {
                abort(403, 'Akses ditolak! Anda tidak berwenang memperbarui soal ini.');
            }
        }

        $request->validate([
            'question_text' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'remove_image' => 'nullable|boolean',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $imagePath = $question->image;

        // Opsi jika guru memilih hapus gambar yang sudah ada
        if ($request->boolean('remove_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        // Jika guru mengunggah gambar baru sebagai pengganti
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $question->update([
            'question_text' => $request->question_text,
            'image' => $imagePath,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => $request->correct_answer,
        ]);

        $targetUrl = $exam ? route('guru.ujian.show', $exam->id) : route('guru.bank-soal.index');
        return redirect($targetUrl)->with('success', 'Soal berhasil diperbarui dan disimpan!');
    }

    /**
     * Menu Bank Soal: Menampilkan card mata pelajaran terlebih dahulu, 
     * lalu ketika mapel diklik menampilkan kumpulan butir soal serta kunci jawabannya.
     */
    public function bankSoal(Request $request): View
    {
        $guru = auth()->user();

        // Ambil daftar mata pelajaran yang diampu guru
        if ($guru->subjects()->exists()) {
            $subjects = $guru->subjects()->orderBy('name')->get();
            $examsQuery = Exam::with(['subject', 'classrooms', 'classroom'])
                ->withCount('questions')
                ->whereIn('subject_id', $guru->subjects->pluck('id'));
        } else {
            $subjects = Subject::orderBy('name')->get();
            $examsQuery = Exam::with(['subject', 'classrooms', 'classroom'])
                ->withCount('questions')
                ->where('user_id', $guru->id);
        }

        // Filter Ujian berdasarkan Mapel jika dipilih
        if ($request->filled('filter_subject_id')) {
            $examsQuery->where('subject_id', $request->filter_subject_id);
        }

        // Pencarian pada daftar ujian
        if ($request->filled('search_exam')) {
            $term = trim($request->search_exam);
            $examsQuery->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhereHas('subject', function ($sq) use ($term) {
                      $sq->where('name', 'like', "%{$term}%");
                  });
            });
        }

        $exams = $examsQuery->latest()->get();

        // Hitung total butir soal dan ujian per mata pelajaran
        foreach ($subjects as $subject) {
            $subject->questions_count = Question::where(function ($q) use ($subject, $guru) {
                $q->where('subject_id', $subject->id)
                  ->orWhereHas('exam', function ($eq) use ($subject, $guru) {
                      $eq->where('subject_id', $subject->id);
                      if (!$guru->subjects()->exists()) {
                          $eq->where('user_id', $guru->id);
                      }
                  });
            })->count();

            $subject->exams_count = Exam::where('subject_id', $subject->id)
                ->where(function ($eq) use ($guru) {
                    if (!$guru->subjects()->exists()) {
                        $eq->where('user_id', $guru->id);
                    }
                })->count();
        }

        // Total seluruh butir soal milik guru
        $totalQuestionsGuru = Question::where(function ($q) use ($guru) {
            if ($guru->subjects()->exists()) {
                $q->whereIn('subject_id', $guru->subjects->pluck('id'))
                  ->orWhereHas('exam', function ($eq) use ($guru) {
                      $eq->whereIn('subject_id', $guru->subjects->pluck('id'));
                  });
            } else {
                $q->whereHas('exam', function ($eq) use ($guru) {
                    $eq->where('user_id', $guru->id);
                });
            }
        })->count();

        // Cek apakah ada ujian atau mapel spesifik yang dipilih untuk melihat butir soal
        $selectedExam = null;
        $selectedSubject = null;

        if ($request->filled('exam_id')) {
            $selectedExam = Exam::with(['subject', 'classrooms', 'classroom'])->find($request->exam_id);
            if ($selectedExam) {
                $selectedSubject = $selectedExam->subject;
            }
        } elseif ($request->filled('subject_id')) {
            $selectedSubject = $subjects->firstWhere('id', (int)$request->subject_id) 
                ?? Subject::find($request->subject_id);
        }

        $query = Question::with(['exam.subject', 'exam.classroom', 'subject']);

        if ($selectedExam) {
            $query->where('exam_id', $selectedExam->id);
        } elseif ($selectedSubject) {
            $subId = $selectedSubject->id;
            $query->where(function ($q) use ($subId) {
                $q->where('subject_id', $subId)
                  ->orWhereHas('exam', function ($eq) use ($subId) {
                      $eq->where('subject_id', $subId);
                  });
            });
        } else {
            if ($guru->subjects()->exists()) {
                $subjectIds = $guru->subjects->pluck('id')->toArray();
                $query->where(function ($q) use ($guru, $subjectIds) {
                    $q->whereHas('exam', function ($eq) use ($guru, $subjectIds) {
                        $eq->where('user_id', $guru->id)
                           ->orWhereIn('subject_id', $subjectIds);
                    })->orWhereIn('subject_id', $subjectIds);
                });
            } else {
                $query->whereHas('exam', function ($eq) use ($guru) {
                    $eq->where('user_id', $guru->id);
                });
            }
        }

        // Pencarian Teks Soal / Opsi
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('option_a', 'like', "%{$search}%")
                  ->orWhere('option_b', 'like', "%{$search}%")
                  ->orWhere('option_c', 'like', "%{$search}%")
                  ->orWhere('option_d', 'like', "%{$search}%");
            });
        }

        $totalQuestions = (clone $query)->count();
        $questions = $query->latest()->paginate(15)->withQueryString();

        return view('guru.bank-soal.index', compact(
            'questions', 
            'totalQuestions', 
            'totalQuestionsGuru',
            'subjects', 
            'exams', 
            'selectedSubject',
            'selectedExam'
        ));
    }

    /**
     * Menu Monitoring Siswa: Melihat status pengerjaan (ongoing vs completed) dan nilai siswa
     */
    public function monitoringIndex(Request $request): View
    {
        $guru = auth()->user();

        $sessionsQuery = ExamSession::with(['user.classroom', 'exam.subject', 'exam.classroom'])
            ->whereHas('exam', function ($eq) use ($guru) {
                if ($guru->subjects()->exists()) {
                    $subjectIds = $guru->subjects->pluck('id')->toArray();
                    $eq->where('user_id', $guru->id)
                       ->orWhereIn('subject_id', $subjectIds);
                } else {
                    $eq->where('user_id', $guru->id);
                }
            });

        // Filter Ujian
        if ($request->filled('exam_id')) {
            $sessionsQuery->where('exam_id', $request->exam_id);
        }

        // Filter Kelas Siswa
        if ($request->filled('classroom_id')) {
            $classId = $request->classroom_id;
            $sessionsQuery->whereHas('user', function ($uq) use ($classId) {
                $uq->where('classroom_id', $classId);
            });
        }

        // Filter Status Sesi (ongoing / completed)
        if ($request->filled('status')) {
            $sessionsQuery->where('status', $request->status);
        }

        // Filter Cari Siswa
        if ($request->filled('search')) {
            $search = trim($request->search);
            $sessionsQuery->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                   ->orWhere('nisn', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Statistik
        $statsBase = clone $sessionsQuery;
        $totalOngoing = (clone $statsBase)->where('status', 'ongoing')->count();
        $totalCompleted = (clone $statsBase)->where('status', 'completed')->count();
        $avgScore = round((float)((clone $statsBase)->where('status', 'completed')->avg('score') ?? 0), 1);

        $sessions = $sessionsQuery->latest()->paginate(15)->withQueryString();

        if ($guru->subjects()->exists()) {
            $exams = Exam::whereIn('subject_id', $guru->subjects->pluck('id'))->orderBy('title')->get();
        } else {
            $exams = Exam::where('user_id', $guru->id)->orderBy('title')->get();
        }
        $classrooms = Classroom::orderBy('name')->get();

        return view('guru.monitoring.index', compact('sessions', 'totalOngoing', 'totalCompleted', 'avgScore', 'exams', 'classrooms'));
    }

    /**
     * Detail Monitoring Siswa: Melihat analisis butir soal yang salah/benar dalam bentuk tabel kotak
     */
    public function monitoringDetail(int $sessionId): View
    {
        $guru = auth()->user();
        $session = ExamSession::with(['user.classroom', 'exam.subject', 'exam.classroom', 'answers.question'])->findOrFail($sessionId);
        $exam = $session->exam;

        // Validasi Otorisasi Guru
        if ($exam && $guru->subjects()->exists()) {
            $allowedSubjectIds = $guru->subjects->pluck('id')->toArray();
            if ($exam->user_id !== $guru->id && !in_array((int)$exam->subject_id, $allowedSubjectIds)) {
                abort(403, 'Akses ditolak!');
            }
        }

        // Ambil semua butir soal ujian
        $questions = Question::where('exam_id', $exam->id)
            ->orWhere(function ($query) use ($exam) {
                $query->whereNull('exam_id')->where('subject_id', $exam->subject_id);
            })
            ->orderBy('id')
            ->get();

        $answersMap = $session->answers->keyBy('question_id');

        $analysis = [];
        $correctCount = 0;
        $wrongCount = 0;
        $unansweredCount = 0;

        foreach ($questions as $index => $q) {
            $answer = $answersMap->get($q->id);
            $selected = $answer ? $answer->selected_answer : null;
            $isCorrect = $answer ? (bool)$answer->is_correct : false;

            if (!empty($selected)) {
                if ($isCorrect) {
                    $status = 'correct';
                    $correctCount++;
                } else {
                    $status = 'wrong';
                    $wrongCount++;
                }
            } else {
                $status = 'unanswered';
                $unansweredCount++;
            }

            $analysis[] = [
                'number' => $index + 1,
                'question' => $q,
                'answer' => $answer,
                'selected' => $selected,
                'is_correct' => $isCorrect,
                'status' => $status,
            ];
        }

        return view('guru.monitoring.detail', compact(
            'session',
            'exam',
            'questions',
            'analysis',
            'correctCount',
            'wrongCount',
            'unansweredCount'
        ));
    }

    /**
     * Rekapitulasi Nilai Siswa untuk Ujian Terpilih
     * Syarat: Jika siswa belum selesai, nilai TIDAK KELUAR di sistem guru
     */
    public function scores(int $examId): View
    {
        $exam = Exam::with(['subject', 'classroom'])->findOrFail($examId);

        $sessions = ExamSession::with('user')
            ->where('exam_id', $examId)
            ->latest()
            ->get();

        return view('guru.exam.scores', compact('exam', 'sessions'));
    }
}
