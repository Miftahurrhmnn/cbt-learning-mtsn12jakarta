<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Pemetaan update nama kelas lama (Romawi / Format SMA) ke format baru
        $renameMap = [
            'Kelas X-A' => '7A',
            'Kelas X-B' => '7B',
            'Kelas XI-A' => '8A',
            'Kelas VII-A' => '7A',
            'Kelas VII-B' => '7B',
            'Kelas VIII-A' => '8A',
            'Kelas VIII-B' => '8B',
            'Kelas IX-A' => '9A',
            'Kelas IX-B' => '9B',
            'X-A' => '7A',
            'X-B' => '7B',
            'XI-A' => '8A',
        ];

        foreach ($renameMap as $oldName => $newName) {
            DB::table('classrooms')
                ->where('name', $oldName)
                ->update(['name' => $newName]);
        }

        // 2. Pastikan kelas wajib (7A, 7B, 8A, 8B, 9A, 9B) sudah terdaftar di database
        $requiredClassrooms = ['7A', '7B', '8A', '8B', '9A', '9B'];
        $now = now();

        foreach ($requiredClassrooms as $name) {
            $exists = DB::table('classrooms')->where('name', $name)->exists();
            if (!$exists) {
                DB::table('classrooms')->insert([
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('classrooms')->where('name', '7A')->update(['name' => 'Kelas X-A']);
        DB::table('classrooms')->where('name', '7B')->update(['name' => 'Kelas X-B']);
        DB::table('classrooms')->where('name', '8A')->update(['name' => 'Kelas XI-A']);
    }
};
