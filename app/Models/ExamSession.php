<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'start_time',
        'end_time',
        'status',
        'score',
        'total_questions',
        'correct_answers',
        'violation_count',
        'last_violation_at',
        'is_cheating_detected',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'last_violation_at' => 'datetime',
        'score' => 'decimal:2',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'violation_count' => 'integer',
        'is_cheating_detected' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getRemainingSecondsAttribute(): int
    {
        if ($this->isCompleted()) {
            return 0;
        }

        $durationMinutes = ($this->exam && $this->exam->duration) ? (int) $this->exam->duration : 60;
        $endTimeLimit = Carbon::parse($this->start_time)->addMinutes($durationMinutes);
        $diff = Carbon::now()->diffInSeconds($endTimeLimit, false);

        return max(0, (int) $diff);
    }

    public function isCheating(): bool
    {
        return $this->violation_count >= 4 || $this->is_cheating_detected;
    }
}
