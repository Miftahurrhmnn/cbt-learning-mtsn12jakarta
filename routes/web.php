<?php

use App\Http\Controllers\Guru\ExamController as GuruExamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Siswa\ExamController as SiswaExamController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Role-based dashboard redirect
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->isGuru()) {
        return redirect()->route('guru.ujian.index');
    }
    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==================== GURU ROUTES ====================
Route::middleware(['auth', 'is_guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('guru.ujian.index');
    })->name('dashboard');

    // Manajemen Ujian (Pilih Mapel & Kelas, Mulai Ujian)
    Route::get('/ujian', [GuruExamController::class, 'index'])->name('ujian.index');
    Route::get('/ujian/create', [GuruExamController::class, 'create'])->name('ujian.create');
    Route::post('/ujian', [GuruExamController::class, 'store'])->name('ujian.store');
    Route::get('/ujian/{id}', [GuruExamController::class, 'show'])->name('ujian.show');
    Route::post('/ujian/{id}/toggle-status', [GuruExamController::class, 'toggleStatus'])->name('ujian.toggle_status');
    Route::delete('/ujian/{id}', [GuruExamController::class, 'destroy'])->name('ujian.destroy');

    // Input Soal Teks / Gambar & Kunci Jawaban
    Route::get('/ujian/{examId}/soal/create', [GuruExamController::class, 'questionCreate'])->name('ujian.soal.create');
    Route::post('/ujian/{examId}/soal', [GuruExamController::class, 'questionStore'])->name('ujian.soal.store');
    Route::delete('/soal/{id}', [GuruExamController::class, 'questionDestroy'])->name('soal.destroy');

    // Rekapitulasi Nilai Siswa
    Route::get('/ujian/{examId}/nilai', [GuruExamController::class, 'scores'])->name('ujian.scores');

    // Fallback URL lama jika diakses
    Route::get('/input-soal', function () {
        return redirect()->route('guru.ujian.index');
    });
});

// ==================== SISWA ROUTES ====================
Route::middleware(['auth', 'is_siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaExamController::class, 'index'])->name('dashboard');

    // Ruang Ujian: Timer 1 jam, Kotak nomor merah -> hijau, AJAX simpan jawaban
    Route::get('/ujian/{id}', [SiswaExamController::class, 'show'])->name('ujian.show');
    Route::post('/ujian/{id}/simpan-jawaban', [SiswaExamController::class, 'saveAnswer'])
        ->middleware('throttle:120,1')
        ->name('ujian.simpan_jawaban');
    Route::post('/ujian/{id}/selesai', [SiswaExamController::class, 'finish'])->name('ujian.selesai');
    Route::get('/ujian/{id}/hasil', [SiswaExamController::class, 'result'])->name('ujian.hasil');
});

// Profile Management (Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
