<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;

class CbtController extends Controller
{
    public function startExam($examId) {
        // User klik 1 mata pelajaran dan kelas berdasarkan ID
        $exam = Exam::with(['subject', 'classroom'])->findOrFail($examId);

        // Ambil bank soal berdasarkan mata pelajaran tersebut, lalu ACAK urutannya
        $questions = Question::where('subject_id', $exam->subject_id)
                                                        ->inRandomOrder()
                                                        ->take(5)
                                                        ->get();

        // Kirim data ke halaman tampilan siswa
        return view('student.exam', compact('exam', 'questions'));
    }     
}
