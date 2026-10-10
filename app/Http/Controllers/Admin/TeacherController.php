<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class TeacherController extends Controller
{
    /**
     * Tampilkan data seluruh guru (Menu: Data Guru)
     */
    public function index(Request $request): View
    {
        $query = User::with(['subjects', 'createdExams'])
            ->where('role', 'guru');

        // Pencarian Nama / Email Guru
        $searchTerm = $request->input('search', $request->input('q'));
        if (!empty($searchTerm)) {
            $search = trim($searchTerm);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan Mata Pelajaran yang diampu
        if ($request->filled('subject_id')) {
            $subjectId = (int)$request->subject_id;
            $query->whereHas('subjects', function ($sq) use ($subjectId) {
                $sq->where('subjects.id', $subjectId);
            });
        }

        $teachers = $query->latest()->paginate(15)->withQueryString();
        $subjects = Subject::orderBy('name')->get();
        $totalTeachers = User::where('role', 'guru')->count();

        return view('admin.guru.index', compact('teachers', 'subjects', 'totalTeachers'));
    }

    /**
     * Tampilkan form tambah data guru baru (Menu: Tambah Guru)
     */
    public function create(): View
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.guru.create', compact('subjects'));
    }

    /**
     * Simpan data guru baru ke database (Khusus Admin)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ], [
            'name.required' => 'Nama lengkap guru wajib diisi.',
            'email.required' => 'Email login guru wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'subject_ids.*.exists' => 'Mata pelajaran yang dipilih tidak valid.',
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'guru',
        ]);

        // Hubungkan mata pelajaran yang dipilih (bisa lebih dari 1 mapel)
        if ($request->has('subject_ids')) {
            $teacher->syncSubjects($request->input('subject_ids', []));
        }

        return redirect()->route('admin.guru.index')
            ->with('success', "Data guru {$teacher->name} berhasil ditambahkan ke database!");
    }

    /**
     * Tampilkan detail atau form edit guru
     */
    public function show(int $id): RedirectResponse
    {
        return redirect()->route('admin.guru.edit', $id);
    }

    /**
     * Tampilkan form edit guru
     */
    public function edit(int $id): View
    {
        $teacher = User::where('role', 'guru')->with('subjects')->findOrFail($id);
        $subjects = Subject::orderBy('name')->get();
        $mySubjectIds = $teacher->subjects->pluck('id')->toArray();

        return view('admin.guru.edit', compact('teacher', 'subjects', 'mySubjectIds'));
    }

    /**
     * Update data guru
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $teacher = User::where('role', 'guru')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($teacher->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ], [
            'name.required' => 'Nama lengkap guru wajib diisi.',
            'email.required' => 'Email login guru wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'subject_ids.*.exists' => 'Mata pelajaran yang dipilih tidak valid.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $teacher->update($data);

        // Sinkronisasi mata pelajaran yang diampu guru
        $teacher->syncSubjects($request->input('subject_ids', []));

        return redirect()->route('admin.guru.index')
            ->with('success', "Data guru {$teacher->name} berhasil diperbarui.");
    }

    /**
     * Hapus data guru
     */
    public function destroy(int $id): RedirectResponse
    {
        $teacher = User::where('role', 'guru')->findOrFail($id);
        $name = $teacher->name;

        // Detach relasi mata pelajaran
        $teacher->subjects()->detach();
        $teacher->delete();

        return redirect()->route('admin.guru.index')
            ->with('success', "Data guru {$name} berhasil dihapus dari sistem.");
    }
}
