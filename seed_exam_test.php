<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;

echo "Menyiapkan data Ujian Uji Coba K6...\n";

// 1. Ambil guru dan mapel
$guru = User::where('role', 'guru')->first();
$subject = Subject::first();
$classrooms = Classroom::all();

if (!$guru || !$subject || $classrooms->isEmpty()) {
    echo "Gagal: Data guru, mata pelajaran, atau kelas belum lengkap.\n";
    exit(1);
}

// 2. Buat Ujian Terbuka untuk seluruh kelas (X-A, X-B, XI-A)
$exam = Exam::updateOrCreate(
    ['title' => 'Simulasi CBT K6 600 Siswa'],
    [
        'subject_id' => $subject->id,
        'user_id' => $guru->id,
        'classroom_id' => $classrooms->first()->id,
        'duration' => 120, // 2 Jam
        'status' => 'published',
        'token' => 'SIMUL60', // Token 7 karakter: SIMUL60
        'day_of_week' => null, // Bebas hari apa saja
        'exam_date' => null, // Fleksibel kapan saja (tidak kadaluwarsa)
        'start_time' => null, // Fleksibel
        'end_time' => null,
    ]
);

// Tautkan ke semua kelas agar 600 siswa bisa mengakses ujian ini
$exam->classrooms()->sync($classrooms->pluck('id')->toArray());

echo "Ujian berhasil dibuat: [ID: {$exam->id}] {$exam->title} (Token: {$exam->token})\n";

// 3. Buat 5 Butir Soal untuk Ujian ini
$sampleQuestions = [
    [
        'question_text' => 'Berapakah hasil dari 25 x 4?',
        'option_a' => '80',
        'option_b' => '100',
        'option_c' => '120',
        'option_d' => '150',
        'correct_answer' => 'B',
    ],
    [
        'question_text' => 'Ibu kota negara Indonesia saat ini adalah?',
        'option_a' => 'Surabaya',
        'option_b' => 'Bandung',
        'option_c' => 'Jakarta',
        'option_d' => 'Medan',
        'correct_answer' => 'C',
    ],
    [
        'question_text' => 'Gas yang dihirup manusia saat bernapas adalah?',
        'option_a' => 'Oksigen (O2)',
        'option_b' => 'Karbon Dioksida (CO2)',
        'option_c' => 'Nitrogen (N2)',
        'option_d' => 'Helium (He)',
        'correct_answer' => 'A',
    ],
    [
        'question_text' => 'Planet terbesar di tata surya kita adalah?',
        'option_a' => 'Bumi',
        'option_b' => 'Mars',
        'option_c' => 'Jupiter',
        'option_d' => 'Saturnus',
        'correct_answer' => 'C',
    ],
    [
        'question_text' => 'Lagu kebangsaan Indonesia Raya diciptakan oleh?',
        'option_a' => 'W.R. Supratman',
        'option_b' => 'Ismail Marzuki',
        'option_c' => 'Kusbini',
        'option_d' => 'C. Simanjuntak',
        'correct_answer' => 'A',
    ],
];

// Bersihkan soal lama jika ada
Question::where('exam_id', $exam->id)->delete();

$createdQuestions = [];
foreach ($sampleQuestions as $qData) {
    $q = Question::create(array_merge($qData, [
        'exam_id' => $exam->id,
        'subject_id' => $subject->id,
    ]));
    $createdQuestions[] = $q->id;
}

Exam::clearExamCache($exam->id);

echo "Berhasil membuat 5 butir soal [ID: " . implode(', ', $createdQuestions) . "]!\n";
file_put_contents(__DIR__ . '/exam_test_config.json', json_encode([
    'exam_id' => $exam->id,
    'token' => $exam->token,
    'question_ids' => $createdQuestions,
], JSON_PRETTY_PRINT));

echo "File konfigurasi berhasil disimpan ke exam_test_config.json\n";
