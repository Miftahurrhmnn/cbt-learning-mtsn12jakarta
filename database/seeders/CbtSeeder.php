<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Question;
use App\Models\Exam;

class CbtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subject = Subject::create(['name' => 'Matematika']);
        $classroom = Classroom::create(['name' => 'Kelas X-A']);

        $exam = Exam::create([
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'duration' => 60
        ]);

        // Buat 7 Pilihan Soal (Nanti sistem akan mengacak & mengambil 5 saja)
        for($i = 1; $i <= 7; $i++ ) {
            Question::create([
                'subject_id' => $subject->id,
                'question_text' => "Ini adalah pertanyaan nomor ke-$i. Berapa hasil dari 1 + $i ?",
                'option_a' => "Jawaban A-$i",
                'option_b' => "Jawaban B-$i",
                'option_c' => "Jawaban C-$i",
                'option_d' => "Jawaban D-$i",
                'correct_answer' => 'A'
            ]);
        }
    }
}
