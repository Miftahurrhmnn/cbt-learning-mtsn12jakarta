<?php

namespace App\Jobs;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProcessStudentImportChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $students;

    /**
     * Create a new job instance.
     *
     * @param array<int, array<string, mixed>> $students
     */
    public function __construct(array $students)
    {
        $this->students = $students;
    }

    /**
     * Execute the job: Batch process student chunks efficiently.
     */
    public function handle(): void
    {
        if (empty($this->students)) {
            return;
        }

        // 1. Preload classrooms map (nama => id)
        $uniqueClassNames = array_unique(array_filter(array_column($this->students, 'classroom')));
        $classroomMap = [];

        if (!empty($uniqueClassNames)) {
            $existingClasses = Classroom::whereIn('name', $uniqueClassNames)->pluck('id', 'name')->toArray();
            foreach ($uniqueClassNames as $cName) {
                if (isset($existingClasses[$cName])) {
                    $classroomMap[$cName] = $existingClasses[$cName];
                } else {
                    $created = Classroom::firstOrCreate(['name' => $cName]);
                    $classroomMap[$cName] = $created->id;
                }
            }
        }

        // 2. Preload existing users by NISN and Email
        $nisns = array_filter(array_column($this->students, 'nisn'));
        $emails = array_filter(array_column($this->students, 'email'));

        $existingUsersByNisn = [];
        if (!empty($nisns)) {
            $existingUsersByNisn = User::whereIn('nisn', $nisns)->get()->keyBy('nisn');
        }

        $existingUsersByEmail = [];
        if (!empty($emails)) {
            $existingUsersByEmail = User::whereIn('email', $emails)->get()->keyBy('email');
        }

        DB::beginTransaction();
        try {
            foreach ($this->students as $item) {
                $name = $item['name'];
                $nisn = $item['nisn'] ?: null;
                $email = $item['email'];
                $className = $item['classroom'];
                $password = $item['password'];
                $classroomId = $className !== '' && isset($classroomMap[$className]) ? $classroomMap[$className] : null;

                // Cek user yang sudah ada
                $existingUser = null;
                if ($nisn && isset($existingUsersByNisn[$nisn])) {
                    $existingUser = $existingUsersByNisn[$nisn];
                } elseif ($email && isset($existingUsersByEmail[$email])) {
                    $existingUser = $existingUsersByEmail[$email];
                }

                if ($existingUser) {
                    // Update user jika role siswa
                    if ($existingUser->role === 'siswa') {
                        $updateData = [
                            'name' => $name,
                            'classroom_id' => $classroomId ?? $existingUser->classroom_id,
                        ];
                        if ($nisn) {
                            $updateData['nisn'] = $nisn;
                        }
                        if ($password !== '') {
                            $updateData['password'] = Hash::make($password);
                        }
                        $existingUser->update($updateData);
                    }
                } else {
                    // Create siswa baru
                    $passToUse = $password !== '' ? $password : ($nisn ?: 'password');
                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'nisn' => $nisn,
                        'classroom_id' => $classroomId,
                        'password' => Hash::make($passToUse),
                        'role' => 'siswa',
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Failed to process student import chunk job: {$e->getMessage()}", [
                'count' => count($this->students),
            ]);
            throw $e;
        }
    }
}
