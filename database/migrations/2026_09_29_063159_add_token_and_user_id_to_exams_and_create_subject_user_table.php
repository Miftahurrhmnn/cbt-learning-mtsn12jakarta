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
        // 1. Tambah token dan user_id (guru pembuat) pada tabel exams
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('classroom_id')->constrained('users')->nullOnDelete();
            $table->string('token', 20)->nullable()->after('status')->index();
        });

        // 2. Buat tabel pivot subject_user untuk relasi Guru dan Mata Pelajaran yang diampu
        Schema::create('subject_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->primary(['user_id', 'subject_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_user');

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'token']);
        });
    }
};
