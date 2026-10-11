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
            $table->boolean('is_locked')->default(false)->after('is_cheating_detected');
            $table->timestamp('locked_at')->nullable()->after('is_locked');
            $table->timestamp('unlocked_at')->nullable()->after('locked_at');
            $table->foreignId('unlocked_by')->nullable()->after('unlocked_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropForeign(['unlocked_by']);
            $table->dropColumn(['is_locked', 'locked_at', 'unlocked_at', 'unlocked_by']);
        });
    }
};
