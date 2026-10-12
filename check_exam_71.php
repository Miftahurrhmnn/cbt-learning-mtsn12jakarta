<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Exam;

$user = User::where('role', 'siswa')->first();
$exam = Exam::find(71);

if (!$exam) {
    echo "Exam 71 not found!\n";
    exit;
}

echo "User: " . $user->name . " | classroom_id: " . var_export($user->classroom_id, true) . "\n";
echo "Exam Title: " . $exam->title . " | token: " . $exam->token . " | status: " . $exam->status . "\n";
echo "Allows Classroom: " . ($exam->allowsClassroom($user->classroom_id) ? "TRUE" : "FALSE") . "\n";
echo "Has Not Started: " . ($exam->hasNotStartedYet() ? "TRUE" : "FALSE") . "\n";
echo "Has Ended: " . ($exam->hasEnded() ? "TRUE" : "FALSE") . "\n";
echo "Exam Date: " . $exam->exam_date . " | Start: " . $exam->start_time . " | End: " . $exam->end_time . "\n";
