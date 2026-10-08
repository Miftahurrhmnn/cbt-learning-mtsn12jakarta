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
     * Get the full Carbon start DateTime (WIB / Asia/Jakarta) based on exam_date and start_time.
     */
    public function getStartDateTime(?\Carbon\Carbon $referenceDate = null): ?\Carbon\Carbon
    {
        $baseDate = $this->exam_date ? $this->exam_date->copy() : ($referenceDate ? $referenceDate->copy() : \Carbon\Carbon::now('Asia/Jakarta'));

        if (!empty($this->start_time)) {
            $time = substr($this->start_time, 0, 5);
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i', $baseDate->format('Y-m-d') . ' ' . $time, 'Asia/Jakarta')->startOfMinute();
        }

        if ($this->exam_date) {
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' 00:00:00', 'Asia/Jakarta')->startOfDay();
        }

        return null;
    }

    /**
     * Get the full Carbon end DateTime (WIB / Asia/Jakarta) based on exam_date, start_time, and end_time.
     * Automatically handles overnight / cross-midnight windows (e.g. 23:00 - 05:00).
     */
    public function getEndDateTime(?\Carbon\Carbon $referenceDate = null): ?\Carbon\Carbon
    {
        $baseDate = $this->exam_date ? $this->exam_date->copy() : ($referenceDate ? $referenceDate->copy() : \Carbon\Carbon::now('Asia/Jakarta'));

        if (!empty($this->end_time)) {
            $endTimeStr = substr($this->end_time, 0, 5);
            $startTimeStr = !empty($this->start_time) ? substr($this->start_time, 0, 5) : null;

            // Jika end_time lebih kecil dari start_time (contoh: 23:00 - 05:00), waktu selesai adalah keesokan harinya (+1 hari)
            if ($startTimeStr && $endTimeStr < $startTimeStr) {
                $baseDate->addDay();
            }

            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' ' . $endTimeStr . ':59', 'Asia/Jakarta');
        }

        if ($this->exam_date) {
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $baseDate->format('Y-m-d') . ' 23:59:59', 'Asia/Jakarta')->endOfDay();
        }

        return null;
    }

    /**
     * Get calculated duration in minutes between start_time and end_time.
     * Automatically handles cross-midnight ranges (e.g. 23:00 - 05:00 = 360 mins).
     */
    public function getCalculatedDurationFromTimes(): ?int
    {
        if (empty($this->start_time) || empty($this->end_time)) {
            return null;
        }

        try {
            $start = \Carbon\Carbon::parse(substr($this->start_time, 0, 5));
            $end = \Carbon\Carbon::parse(substr($this->end_time, 0, 5));
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            $diff = (int) $start->diffInMinutes($end, false);
            return $diff > 0 ? $diff : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if exam has not reached its scheduled start time yet (WIB / Asia/Jakarta).
     */
    public function hasNotStartedYet(): bool
    {
        $now = \Carbon\Carbon::now('Asia/Jakarta');

        // 1. Jika ada tanggal ujian (exam_date)
        if ($this->exam_date) {
            $startDateTime = $this->getStartDateTime();
            if ($startDateTime) {
                return $now->lt($startDateTime);
            }
            return false;
        }

        // 2. Jika tanggal fleksibel / tanpa tanggal
        if (!empty($this->start_time) && !empty($this->end_time)) {
            $currentTime = $now->format('H:i');
            $startTime = substr($this->start_time, 0, 5);
            $endTime = substr($this->end_time, 0, 5);

            // Rentang lintas tengah malam (contoh: 23:00 - 05:00)
            if ($endTime < $startTime) {
                // Di luar jam aktif (siang hari): antara > 05:00 dan < 23:00
                return ($currentTime > $endTime && $currentTime < $startTime);
            }

            // Rentang normal hari yang sama (contoh: 08:00 - 10:00)
            return $currentTime < $startTime;
        }

        if (!empty($this->start_time)) {
            $currentTime = $now->format('H:i');
            $startTime = substr($this->start_time, 0, 5);
            return $currentTime < $startTime;
        }

        return false;
    }

    /**
     * Check if exam has already ended according to end_time or exam_date (WIB / Asia/Jakarta).
     */
    public function hasEnded(): bool
    {
        $now = \Carbon\Carbon::now('Asia/Jakarta');

        // 1. Jika ada tanggal ujian (exam_date)
        if ($this->exam_date) {
            $endDateTime = $this->getEndDateTime();
            if ($endDateTime) {
                return $now->gt($endDateTime);
            }
            return false;
        }

        // 2. Jika tanggal fleksibel / tanpa tanggal
        if (!empty($this->start_time) && !empty($this->end_time)) {
            $currentTime = $now->format('H:i');
            $startTime = substr($this->start_time, 0, 5);
            $endTime = substr($this->end_time, 0, 5);

            // Rentang lintas tengah malam (contoh: 23:00 - 05:00)
            if ($endTime < $startTime) {
                // Jam aktif adalah >= 23:00 atau <= 05:00.
                // Ujian tidak dianggap berakhir selama di jam aktif.
                return false;
            }

            // Rentang normal hari yang sama (contoh: 08:00 - 10:00)
            return $currentTime > $endTime;
        }

        if (!empty($this->end_time)) {
            $currentTime = $now->format('H:i');
            $endTime = substr($this->end_time, 0, 5);
            return $currentTime > $endTime;
        }

        return false;
    }

    /**
     * Check if current time is within exam schedule window (if defined)
     */
    public function isWithinTimeWindow(): bool
    {
        return !$this->hasNotStartedYet() && !$this->hasEnded();
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
