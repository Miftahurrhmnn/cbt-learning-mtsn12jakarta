<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama Dashboard Administrator
     */
    public function index(Request $request): View
    {
        $totalStudents = User::where('role', 'siswa')->count();
        $totalClassrooms = Classroom::count();
        $totalTeachers = User::where('role', 'guru')->count();
        $totalExams = Exam::count();

        // 5 Siswa terbaru yang baru didaftarkan
        $recentStudents = User::with('classroom')
            ->where('role', 'siswa')
            ->latest()
            ->take(5)
            ->get();

        // Data rombel/kelas dengan jumlah siswa
        $classrooms = Classroom::withCount('students')
            ->orderBy('name')
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalClassrooms',
            'totalTeachers',
            'totalExams',
            'recentStudents',
            'classrooms'
        ));
    }
}
