<?php

namespace App\Jobs;

use App\Models\ExamAnswer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessStudentAnswerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $sessionId;
    public int $questionId;
    public ?string $selectedAnswer;
    public bool $isCorrect;

    /**
     * Create a new job instance.
     */
    public function __construct(int $sessionId, int $questionId, ?string $selectedAnswer, bool $isCorrect)
    {
        $this->sessionId = $sessionId;
        $this->questionId = $questionId;
        $this->selectedAnswer = $selectedAnswer;
        $this->isCorrect = $isCorrect;
    }

    /**
     * Execute the job: Upsert answer atomic and fast into database.
     */
    public function handle(): void
    {
        try {
            ExamAnswer::upsert(
                [
                    [
                        'exam_session_id' => $this->sessionId,
                        'question_id' => $this->questionId,
                        'selected_answer' => $this->selectedAnswer,
                        'is_correct' => $this->isCorrect,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ],
                ['exam_session_id', 'question_id'],
                ['selected_answer', 'is_correct', 'updated_at']
            );
        } catch (\Throwable $e) {
            Log::error("Failed to process student answer in queue: {$e->getMessage()}", [
                'session_id' => $this->sessionId,
                'question_id' => $this->questionId,
            ]);
            throw $e;
        }
    }
}
