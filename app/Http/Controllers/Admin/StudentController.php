<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Tampilkan data seluruh siswa
     */
    public function index(Request $request): View
    {
        $query = User::with('classroom')->where('role', 'siswa');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        $students = $query->latest()->paginate(15)->withQueryString();
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.siswa.index', compact('students', 'classrooms'));
    }

    /**
     * Tampilkan form tambah data siswa baru (Khusus Admin)
     */
    public function create(): View
    {
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.siswa.create', compact('classrooms'));
    }

    /**
     * Simpan data siswa baru ke database (Khusus Admin)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'nisn' => ['nullable', 'string', 'max:30', 'unique:users,nisn'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'classroom_id' => $request->classroom_id,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
        ]);

        return redirect()->route('admin.siswa.index')
            ->with('success', "Data siswa {$request->name} berhasil disimpan ke database!");
    }

    /**
     * Tampilkan form edit siswa
     */
    public function edit(int $id): View
    {
        $student = User::where('role', 'siswa')->findOrFail($id);
        $classrooms = Classroom::orderBy('name')->get();

        return view('admin.siswa.edit', compact('student', 'classrooms'));
    }

    /**
     * Update data siswa
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $student = User::where('role', 'siswa')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($student->id)],
            'nisn' => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($student->id)],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'nisn' => $request->nisn,
            'classroom_id' => $request->classroom_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $student->update($data);

        return redirect()->route('admin.siswa.index')
            ->with('success', "Data siswa {$student->name} berhasil diperbarui.");
    }

    /**
     * Hapus data siswa
     */
    public function destroy(int $id): RedirectResponse
    {
        $student = User::where('role', 'siswa')->findOrFail($id);
        $name = $student->name;
        $student->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', "Data siswa {$name} berhasil dihapus dari sistem.");
    }
}
