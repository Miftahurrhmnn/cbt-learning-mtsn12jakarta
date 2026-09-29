<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\Subject;

class QuestionController extends Controller
{
    // 1. Menampilkan Halaman Formulir Input Soal
    public function create() {
        $subjects = Subject::all();
        return view('guru.input-soal', compact('subjects'));
    }

    // 2. Menyimpan Soal yang Dikirim oleh Guru ke Database
    public function store(Request $request) {

        // Validasi input data dari form agar tidak ada yang kosong
        $request->validate([
            'subject_id' => 'required',
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D'
        ]);
    }

    // Simpan ke tabel questions
    Queston::create($request->all());

    // Kembalikan ke halaman form dengan pesan sukses
    return redirect()->back()->with('success', 'Soal berhasil disimpan ke Bank Soal!');
}
