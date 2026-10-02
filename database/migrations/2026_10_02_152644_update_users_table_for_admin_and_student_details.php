<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ubah tipe role menjadi string agar fleksibel mendukung admin, guru, siswa
            $table->string('role', 20)->default('siswa')->change();
            
            // Tambahkan NISN dan Kelas untuk Siswa
            $table->string('nisn', 30)->nullable()->after('email');
            $table->foreignId('classroom_id')->nullable()->after('nisn')->constrained('classrooms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['classroom_id']);
            $table->dropColumn(['classroom_id', 'nisn']);
            $table->enum('role', ['guru', 'siswa'])->default('siswa')->change();
        });
    }
};
