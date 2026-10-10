<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nisn',
        'classroom_id',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }

    /**
     * Mata pelajaran yang diampu oleh Guru ini
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_user');
    }

    /**
     * Ujian yang dibuat oleh Guru ini
     */
    public function createdExams(): HasMany
    {
        return $this->hasMany(Exam::class, 'user_id');
    }

    /**
     * Cek apakah guru mengampu mata pelajaran tertentu
     */
    public function teachesSubject(int $subjectId): bool
    {
        return $this->subjects()->where('subjects.id', $subjectId)->exists();
    }

    /**
     * Tambahkan satu mata pelajaran ke guru jika belum ada
     */
    public function assignSubject(int $subjectId): void
    {
        if (!$this->teachesSubject($subjectId)) {
            $this->subjects()->attach($subjectId);
        }
    }

    /**
     * Sinkronisasi daftar mata pelajaran yang diampu guru (bisa lebih dari 1)
     */
    public function syncSubjects(array $subjectIds): void
    {
        $this->subjects()->sync($subjectIds);
    }
}
