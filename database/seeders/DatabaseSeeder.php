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
        $guru1 = User::updateOrCreate(
            ['email' => 'emarohimah@teacher.mtsn12.sch.id'],
            [
                'name' => 'Ema Rohimah, S.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru2 = User::updateOrCreate(
            ['email' => 'nuranisah@teacher.mtsn12.sch.id'],
            [
                'name' => 'Hj. Nur Anisah, M.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru3 = User::updateOrCreate(
            ['email' => 'rahman@teacher.mtsn12.sch.id'],
            [
                'name' => 'Rahman, S.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru4 = User::updateOrCreate(
            ['email' => 'itanovitasari@teacher.mtsn12.sch.id'],
            [
                'name' => 'Ita Novitasari, S.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru5 = User::updateOrCreate(
            ['email' => 'arifsulaiman@teacher.mtsn12.sch.id'],
            [
                'name' => 'Arif Sulaiman, S.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru6 = User::updateOrCreate(
            ['email' => 'ekanursabila@teacher.mtsn12.sch.id'],
            [
                'name' => 'Prisillia Eka Nursabila',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru7 = User::updateOrCreate(
            ['email' => 'hazami@teacher.mtsn12.sch.id'],
            [
                'name' => 'Hazami, M.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru8 = User::updateOrCreate(
            ['email' => 'seftiharneli@teacher.mtsn12.sch.id'],
            [
                'name' => 'Sefti Harneli, M.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru9 = User::updateOrCreate(
            ['email' => 'karyati@teacher.mtsn12.sch.id'],
            [
                'name' => 'Karyati, M.Pd.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru10 = User::updateOrCreate(
            ['email' => 'buchori@teacher.mtsn12.sch.id'],
            [
                'name' => 'H. Buchori, S.Ag.',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );
        $guru11 = User::updateOrCreate(
            ['email' => 'ruslaini@teacher.mtsn12.sch.id'],
            [
                'name' => 'Dr. Ruslaini, M.M., M.Pd.',
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
        $mapelIPS = Subject::firstOrCreate(['name' => 'Ilmu Pengetahuan Sosial (IPS)']);
        $mapelAgama = Subject::firstOrCreate(['name' => 'Pendidikan Agama Islam (PAI)']);

        if (isset($guru3)) {
            $guru3->subjects()->sync([$mapelMatematika->id]);
        }

        if (isset($guru10)) {
            $guru10->subjects()->sync([$mapelAgama->id]);
        }
    }
}
