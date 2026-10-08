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
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->unsignedInteger('violation_count')->default(0)->after('correct_answers');
            $table->timestamp('last_violation_at')->nullable()->after('violation_count');
            $table->boolean('is_cheating_detected')->default(false)->after('last_violation_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn(['violation_count', 'last_violation_at', 'is_cheating_detected']);
        });
    }
};
