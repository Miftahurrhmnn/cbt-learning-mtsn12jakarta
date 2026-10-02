<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Akun Administrator
        $admin = User::updateOrCreate(
            ['email' => 'admin@cbt.test'],
            [
                'name' => 'Administrator CBT',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 1. Akun Guru & Siswa
        $guru = User::updateOrCreate(
            ['email' => 'guru@cbt.test'],
            [
                'name' => 'Bapak Budi, S.Pd. (Guru)',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );

        // Akun Guru IPA (untuk verifikasi pembatasan mapel)
        $guruIPA = User::updateOrCreate(
            ['email' => 'guru.ipa@cbt.test'],
            [
                'name' => 'Ibu Siti Aminah, M.Pd. (Guru IPA)',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );

        // Update user guru@gmail.com jika ada dari percobaan sebelumnya
        User::where('email', 'guru@gmail.com')->update(['role' => 'guru']);

        // Data Master Kelas
        $kelasXA = Classroom::firstOrCreate(['name' => 'Kelas X-A']);
        $kelasXB = Classroom::firstOrCreate(['name' => 'Kelas X-B']);
        $kelasXIA = Classroom::firstOrCreate(['name' => 'Kelas XI-A']);

        $siswa = User::updateOrCreate(
            ['email' => 'siswa@cbt.test'],
            [
                'name' => 'Ahmad Fauzi (Siswa)',
                'nisn' => '41524110008',
                'classroom_id' => $kelasXA->id,
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ]
        );

        // 2. Data Master Mata Pelajaran
        $mapelMatematika = Subject::firstOrCreate(['name' => 'Matematika']);
        $mapelBIndo = Subject::firstOrCreate(['name' => 'Bahasa Indonesia']);
        $mapelIPA = Subject::firstOrCreate(['name' => 'Ilmu Pengetahuan Alam (IPA)']);

        // Tugaskan Guru ke Mata Pelajaran Masing-Masing
        // Bapak Budi & Miftah HANYA mengajar Matematika
        $guru->subjects()->sync([$mapelMatematika->id]);

        // Ibu Siti HANYA mengajar IPA
        $guruIPA->subjects()->sync([$mapelIPA->id]);

        // 4. Contoh Ujian yang sudah Aktif (Published) dengan Token
        $examMatematika = Exam::firstOrCreate(
            [
                'subject_id' => $mapelMatematika->id,
                'classroom_id' => $kelasXA->id,
            ],
            [
                'user_id' => $guru->id,
                'title' => 'Penilaian Harian Matematika Dasar',
                'duration' => 60, // 1 Jam
                'status' => 'published',
                'token' => 'MTK24',
            ]
        );

        // Pastikan token dan user_id terisi jika ujian sudah ada sebelumnya
        if (empty($examMatematika->token) || empty($examMatematika->user_id)) {
            $examMatematika->update([
                'user_id' => $guru->id,
                'token' => 'MTK24',
            ]);
        }

        // 5. Soal-Soal Ujian
        $sampleQuestions = [
            [
                'question_text' => 'Berapakah hasil dari 25 + 15 x 2 ?',
                'option_a' => '80',
                'option_b' => '55',
                'option_c' => '65',
                'option_d' => '70',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'Jika sebuah segitiga memiliki alas 10 cm dan tinggi 8 cm, berapa luas segitiga tersebut?',
                'option_a' => '40 cm²',
                'option_b' => '80 cm²',
                'option_c' => '20 cm²',
                'option_d' => '60 cm²',
                'correct_answer' => 'A',
            ],
            [
                'question_text' => 'Berapakah nilai x yang memenuhi persamaan 3x - 5 = 16 ?',
                'option_a' => '5',
                'option_b' => '6',
                'option_c' => '7',
                'option_d' => '8',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'Berapa persen nilai 15 dari 60?',
                'option_a' => '20%',
                'option_b' => '25%',
                'option_c' => '30%',
                'option_d' => '35%',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'Sebuah kubus memiliki panjang rusuk 6 cm. Berapakah volume kubus tersebut?',
                'option_a' => '36 cm³',
                'option_b' => '144 cm³',
                'option_c' => '216 cm³',
                'option_d' => '256 cm³',
                'correct_answer' => 'C',
            ],
        ];

        foreach ($sampleQuestions as $q) {
            Question::firstOrCreate(
                [
                    'exam_id' => $examMatematika->id,
                    'question_text' => $q['question_text'],
                ],
                [
                    'subject_id' => $mapelMatematika->id,
                    'option_a' => $q['option_a'],
                    'option_b' => $q['option_b'],
                    'option_c' => $q['option_c'],
                    'option_d' => $q['option_d'],
                    'correct_answer' => $q['correct_answer'],
                ]
            );
        }
    }
}
