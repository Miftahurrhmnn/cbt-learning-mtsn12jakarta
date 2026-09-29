<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Tampilkan daftar seluruh ujian yang relevan dengan mata pelajaran guru
     */
    public function index(): View
    {
        $guru = auth()->user();
        $query = Exam::with(['subject', 'classroom', 'teacher'])
            ->withCount(['questions', 'sessions']);

        // Jika guru memiliki mata pelajaran yang diampu, hanya tampilkan ujian mapel tersebut
        if ($guru->subjects()->exists()) {
            $subjectIds = $guru->subjects->pluck('id')->toArray();
            $query->where(function ($q) use ($guru, $subjectIds) {
                $q->where('user_id', $guru->id)
                  ->orWhereIn('subject_id', $subjectIds);
            });
        }

        $exams = $query->latest()->paginate(10);

        return view('guru.exam.index', compact('exams'));
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
        $defaultToken = Exam::generateToken();

        return view('guru.exam.create', compact('subjects', 'classrooms', 'defaultToken'));
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
            'classroom_id' => 'required|exists:classrooms,id',
            'duration' => 'required|integer|min:5|max:300',
            'status' => 'required|in:draft,published',
            'token' => 'nullable|string|max:20',
        ]);

        // VALIDASI KEAMANAN OTORISASI:
        // Pastikan guru tidak bisa memilih mata pelajaran di luar yang diampunya
        if ($guru->subjects()->exists()) {
            $allowedSubjectIds = $guru->subjects->pluck('id')->toArray();
            if (!in_array((int)$request->subject_id, $allowedSubjectIds)) {
                return back()->withInput()->with('error', 'Akses ditolak! Anda hanya berwenang membuat ujian pada mata pelajaran yang Anda ampu.');
            }
        }

        $token = $request->filled('token') ? strtoupper(trim($request->token)) : Exam::generateToken();

        $exam = Exam::create([
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'classroom_id' => $request->classroom_id,
            'user_id' => $guru->id,
            'duration' => $request->duration,
            'status' => $request->status,
            'token' => $token,
        ]);

        return redirect()->route('guru.ujian.show', $exam->id)
            ->with('success', "Ujian berhasil dibuat dengan Token [ {$token} ]! Silakan tambahkan butir soal.");
    }

    /**
     * Tampilkan detail ujian & bank soal ujian tersebut
     */
    public function show(int $id): View
    {
        $exam = Exam::with(['subject', 'classroom', 'questions'])->findOrFail($id);

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
