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
        'day_of_week',
        'exam_date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'duration' => 'integer',
        'exam_date' => 'date',
    ];

    public static function daysList(): array
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function classrooms(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_exam');
    }

    /**
     * Get all assigned classrooms (including primary classroom and pivot classrooms)
     */
    public function getAllClassroomNamesAttribute(): string
    {
        $names = collect();
        if ($this->classroom) {
            $names->push($this->classroom->name);
        }
        $pivotClassrooms = $this->relationLoaded('classrooms') ? $this->classrooms : $this->classrooms()->get();
        foreach ($pivotClassrooms as $cls) {
            if (!$names->contains($cls->name)) {
                $names->push($cls->name);
            }
        }
        return $names->isNotEmpty() ? $names->implode(', ') : '-';
    }

    /**
     * Get formatted time window status (e.g. '07:30 - 09:30 WIB' or 'Waktu Fleksibel')
     */
    public function getFormattedTimeRangeAttribute(): string
    {
        if (!empty($this->start_time) && !empty($this->end_time)) {
            $start = substr($this->start_time, 0, 5);
            $end = substr($this->end_time, 0, 5);
            return "{$start} - {$end} WIB";
        } elseif (!empty($this->start_time)) {
            $start = substr($this->start_time, 0, 5);
            return "Mulai {$start} WIB";
        } elseif (!empty($this->end_time)) {
            $end = substr($this->end_time, 0, 5);
            return "Sampai {$end} WIB";
        }
        return 'Fleksibel';
    }

    /**
     * Check if a classroom ID is allowed to take this exam
     */
    public function allowsClassroom(?int $classroomId): bool
    {
        if (!$classroomId) {
            return false;
        }

        if ($this->classroom_id == $classroomId) {
            return true;
        }

        return $this->classrooms()->where('classrooms.id', $classroomId)->exists();
    }

    /**
     * Check if current time is within exam schedule window (if defined)
     */
    public function isWithinTimeWindow(): bool
    {
        if (empty($this->start_time) && empty($this->end_time)) {
            return true; // No time restriction
        }

        $now = now()->format('H:i:s');
        if (!empty($this->start_time) && $now < $this->start_time) {
            return false;
        }
        if (!empty($this->end_time) && $now > $this->end_time) {
            return false;
        }

        return true;
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
     * Generate token acak 6-7 karakter unik untuk ujian
     */
    public static function generateToken(): string
    {
        do {
            $token = strtoupper(Str::random(6));
        } while (self::where('token', $token)->exists());

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
