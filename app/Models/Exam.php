<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'subject_id',
        'classroom_id',
        'user_id',
        'token',
        'duration',
        'status',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->title) {
            return $this->title;
        }

        $subjectName = $this->subject ? $this->subject->name : 'Mata Pelajaran';
        $className = $this->classroom ? $this->classroom->name : 'Kelas';

        return "Ujian {$subjectName} ({$className})";
    }

    /**
     * Generate token ujian acak 6 karakter unik tanpa karakter ambigu
     */
    public static function generateToken(int $length = 6): string
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $token = '';
        for ($i = 0; $i < $length; $i++) {
            $token .= $pool[random_int(0, strlen($pool) - 1)];
        }
        return $token;
    }

    /**
     * Periksa apakah token yang dimasukkan siswa cocok
     */
    public function isValidToken(?string $inputToken): bool
    {
        if (empty($this->token)) {
            return true; // Jika ujian tidak memakai token
        }

        return strtoupper(trim((string)$inputToken)) === strtoupper(trim($this->token));
    }
}
