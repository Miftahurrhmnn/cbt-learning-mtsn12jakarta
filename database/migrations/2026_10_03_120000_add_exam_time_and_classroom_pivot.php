<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('start_time', 10)->nullable()->after('exam_date');
            $table->string('end_time', 10)->nullable()->after('start_time');
        });

        Schema::create('classroom_exam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['exam_id', 'classroom_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_exam');

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
};
