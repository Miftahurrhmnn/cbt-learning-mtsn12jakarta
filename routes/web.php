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
    if ($user->isAdmin()) {
        return redirect()->route('admin.siswa.index');
    }
    if ($user->isGuru()) {
        return redirect()->route('guru.ujian.index');
    }
    return redirect()->route('siswa.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ==================== ADMIN ROUTES (KHUSUS ROLE ADMIN) ====================
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.siswa.index');
    })->name('dashboard');

    Route::resource('siswa', \App\Http\Controllers\Admin\StudentController::class);
});

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
    Route::get('/ujian/soal/template-docx', [GuruExamController::class, 'downloadDocxTemplate'])->name('ujian.soal.template');
    Route::get('/ujian/{examId}/soal/create', [GuruExamController::class, 'questionCreate'])->name('ujian.soal.create');
    Route::post('/ujian/{examId}/soal', [GuruExamController::class, 'questionStore'])->name('ujian.soal.store');
    Route::post('/ujian/{examId}/soal/import-docx', [GuruExamController::class, 'questionImportDocx'])->name('ujian.soal.import_docx');
    Route::get('/soal/{id}/edit', [GuruExamController::class, 'questionEdit'])->name('soal.edit');
    Route::put('/soal/{id}', [GuruExamController::class, 'questionUpdate'])->name('soal.update');
    Route::delete('/soal/{id}', [GuruExamController::class, 'questionDestroy'])->name('soal.destroy');

    // Bank Soal Guru
    Route::get('/bank-soal', [GuruExamController::class, 'bankSoal'])->name('bank-soal.index');

    // Monitoring Siswa (Real-time aktivitas siswa & analisis jawaban tabel kotak)
    Route::get('/monitoring', [GuruExamController::class, 'monitoringIndex'])->name('monitoring.index');
    Route::get('/monitoring/sesi/{sessionId}', [GuruExamController::class, 'monitoringDetail'])->name('monitoring.detail');

    // Rekapitulasi Nilai Siswa
    Route::get('/ujian/{examId}/nilai', [GuruExamController::class, 'scores'])->name('ujian.scores');

    // Fallback URL lama jika diakses
    Route::get('/input-soal', function () {
        return redirect()->route('guru.ujian.index');
    });
});

// ==================== SISWA ROUTES ====================
Route::middleware(['auth', 'is_siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaExamController::class, 'index'])
        ->name('dashboard');

    // Verifikasi token sebelum masuk ruang ujian
    Route::get('/ujian/{id}/token', [SiswaExamController::class, 'token'])
        ->name('ujian.token');

    Route::post('/ujian/{id}/verify-token', [SiswaExamController::class, 'verifyToken'])
        ->name('ujian.verify-token');

    // Ruang Ujian: Timer 1 jam, Kotak nomor merah -> hijau, AJAX simpan jawaban
    Route::get('/ujian/{id}', [SiswaExamController::class, 'show'])
        ->name('ujian.show');

    Route::post('/ujian/{id}/simpan-jawaban', [SiswaExamController::class, 'saveAnswer'])
        ->middleware('throttle:120,1')
        ->name('ujian.simpan_jawaban');

    Route::post('/ujian/{id}/selesai', [SiswaExamController::class, 'finish'])
        ->name('ujian.selesai');

    Route::get('/ujian/{id}/hasil', [SiswaExamController::class, 'result'])
        ->name('ujian.hasil');
});

// Profile Management (Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
