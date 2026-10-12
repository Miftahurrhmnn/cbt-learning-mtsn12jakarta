<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: Add composite & covering indexes to support 500+ concurrent CBT users.
     */
    public function up(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->unique(['exam_session_id', 'question_id'], 'uq_exam_answers_session_question');
        });

        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->index(['user_id', 'exam_id'], 'idx_exam_sessions_user_exam');
            $table->index(['exam_id', 'status'], 'idx_exam_sessions_exam_status');
            $table->index(['status', 'is_locked'], 'idx_exam_sessions_status_locked');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->index(['status', 'classroom_id'], 'idx_exams_status_classroom');
            $table->index(['status', 'day_of_week'], 'idx_exams_status_day');
            $table->index(['user_id', 'status'], 'idx_exams_user_status');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index(['exam_id', 'subject_id'], 'idx_questions_exam_subject');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'classroom_id'], 'idx_users_role_classroom');
            $table->index(['nisn'], 'idx_users_nisn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_nisn');
            $table->dropIndex('idx_users_role_classroom');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_exam_subject');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex('idx_exams_user_status');
            $table->dropIndex('idx_exams_status_day');
            $table->dropIndex('idx_exams_status_classroom');
        });

        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_exam_sessions_status_locked');
            $table->dropIndex('idx_exam_sessions_exam_status');
            $table->dropIndex('idx_exam_sessions_user_exam');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropUnique('uq_exam_answers_session_question');
        });
    }
};
