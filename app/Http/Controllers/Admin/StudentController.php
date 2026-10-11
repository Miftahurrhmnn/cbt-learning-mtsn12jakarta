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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    /**
     * Tampilkan halaman import siswa (Excel/CSV)
     */
    public function import(): View
    {
        $classrooms = Classroom::orderBy('name')->get();
        return view('admin.siswa.import', compact('classrooms'));
    }

    /**
     * Unduh file template untuk import siswa (Excel/CSV)
     */
    public function downloadTemplate(string $type)
    {
        $sampleData = [
            ['Ahmad Fauzi', '0081234567', 'Kelas X-A', 'ahmad@siswa.mtsn12.sch.id', 'password123'],
            ['Siti Nurhaliza', '0087654321', 'Kelas X-B', 'siti@siswa.mtsn12.sch.id', 'password123'],
            ['Budi Santoso', '0089988776', 'Kelas X-A', '', ''],
        ];

        if ($type === 'csv') {
            $filename = 'template_import_siswa.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            $columns = ['Nama Lengkap', 'NISN', 'Kelas', 'Email', 'Password'];

            return response()->stream(function () use ($columns, $sampleData) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM agar Excel di Windows membaca aksen dan karakter dengan benar
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
                fputcsv($file, $columns);
                foreach ($sampleData as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            }, 200, $headers);
        }

        // Default to Excel .xlsx
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');

        $headers = ['Nama Lengkap', 'NISN', 'Kelas', 'Email', 'Password'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->fromArray($sampleData, null, 'A2');

        // Styling Header Row
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1B3A6B']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Format kolom NISN sebagai teks eksplisit agar angka 0 di depan tidak terpotong
        $sheet->getStyle('B2:B100')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_siswa.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Proses import file Excel / CSV data siswa
     */
    public function processImport(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120'],
        ], [
            'file.required' => 'Silakan pilih file Excel (.xlsx, .xls) atau CSV (.csv) terlebih dahulu.',
            'file.mimes' => 'Format file yang diunggah harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max' => 'Ukuran file tidak boleh melebihi 5 MB.',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withErrors(['file' => 'Gagal membaca isi file: ' . $e->getMessage()]);
        }

        if (count($rows) < 2) {
            return redirect()->back()
                ->withErrors(['file' => 'File tidak memiliki baris data siswa atau file kosong.']);
        }

        // Tentukan mapping kolom dari baris header pertama
        $headerRow = array_shift($rows);
        $colMap = [
            'name' => null,
            'nisn' => null,
            'classroom' => null,
            'email' => null,
            'password' => null,
        ];

        foreach ($headerRow as $colKey => $colHeader) {
            $headerLower = strtolower(trim((string)$colHeader));
            if (str_contains($headerLower, 'nama') || str_contains($headerLower, 'name')) {
                $colMap['name'] = $colKey;
            } elseif (str_contains($headerLower, 'nisn') || str_contains($headerLower, 'nis')) {
                $colMap['nisn'] = $colKey;
            } elseif (str_contains($headerLower, 'kelas') || str_contains($headerLower, 'class')) {
                $colMap['classroom'] = $colKey;
            } elseif (str_contains($headerLower, 'email') || str_contains($headerLower, 'surel')) {
                $colMap['email'] = $colKey;
            } elseif (str_contains($headerLower, 'pass') || str_contains($headerLower, 'sandi')) {
                $colMap['password'] = $colKey;
            }
        }

        // Fallback default jika header tidak terdeteksi
        $keys = array_keys($headerRow);
        if (!$colMap['name'] && isset($keys[0])) $colMap['name'] = $keys[0];
        if (!$colMap['nisn'] && isset($keys[1])) $colMap['nisn'] = $keys[1];
        if (!$colMap['classroom'] && isset($keys[2])) $colMap['classroom'] = $keys[2];
        if (!$colMap['email'] && isset($keys[3])) $colMap['email'] = $keys[3];
        if (!$colMap['password'] && isset($keys[4])) $colMap['password'] = $keys[4];

        $insertedCount = 0;
        $updatedCount = 0;
        $errorList = [];
        $rowNum = 1;

        foreach ($rows as $row) {
            $rowNum++;

            $name = trim((string)($row[$colMap['name']] ?? ''));
            $nisn = trim((string)($row[$colMap['nisn']] ?? ''));
            $className = trim((string)($row[$colMap['classroom']] ?? ''));
            $email = trim((string)($row[$colMap['email']] ?? ''));
            $password = trim((string)($row[$colMap['password']] ?? ''));

            // Lewati baris yang seluruhnya kosong
            if ($name === '' && $nisn === '' && $className === '' && $email === '') {
                continue;
            }

            // Validasi kolom Nama
            if ($name === '') {
                $errorList[] = "Baris #{$rowNum}: Kolom Nama wajib diisi.";
                continue;
            }

            // Otomatisasi Email jika dikosongkan
            if ($email === '') {
                if ($nisn !== '') {
                    $email = strtolower($nisn) . '@siswa.mtsn12.sch.id';
                } else {
                    $sanitized = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($name));
                    $email = ($sanitized ?: 'siswa') . rand(100, 999) . '@siswa.mtsn12.sch.id';
                }
            } else {
                $email = strtolower($email);
            }

            // Validasi format email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorList[] = "Baris #{$rowNum}: Format email ({$email}) tidak valid.";
                continue;
            }

            // Penanganan Kelas (buat otomatis jika belum ada)
            $classroomId = null;
            if ($className !== '') {
                $classroom = Classroom::firstOrCreate(['name' => $className]);
                $classroomId = $classroom->id;
            }

            // Cek apakah email bentrok dengan akun Admin atau Guru
            $existingNonStudent = User::where('email', $email)
                ->whereIn('role', ['admin', 'guru'])
                ->first();
            if ($existingNonStudent) {
                $errorList[] = "Baris #{$rowNum}: Email {$email} sudah digunakan oleh akun {$existingNonStudent->role}.";
                continue;
            }

            // Cek apakah siswa sudah ada di database (berdasarkan NISN atau Email)
            $existingStudent = null;
            if ($nisn !== '') {
                $existingStudent = User::where('role', 'siswa')->where('nisn', $nisn)->first();
            }
            if (!$existingStudent) {
                $existingStudent = User::where('role', 'siswa')->where('email', $email)->first();
            }

            if ($existingStudent) {
                $updateData = [
                    'name' => $name,
                    'classroom_id' => $classroomId ?? $existingStudent->classroom_id,
                ];
                if ($nisn !== '') {
                    $updateData['nisn'] = $nisn;
                }
                if ($email !== '' && $existingStudent->email !== $email) {
                    $emailTaken = User::where('email', $email)->where('id', '!=', $existingStudent->id)->exists();
                    if (!$emailTaken) {
                        $updateData['email'] = $email;
                    }
                }
                if ($password !== '') {
                    $updateData['password'] = Hash::make($password);
                }
                $existingStudent->update($updateData);
                $updatedCount++;
            } else {
                // Siswa baru
                $emailExists = User::where('email', $email)->exists();
                if ($emailExists) {
                    $email = ($nisn ?: 'siswa') . '_' . rand(10, 99) . '@siswa.mtsn12.sch.id';
                }

                $passToUse = $password !== '' ? $password : ($nisn !== '' ? $nisn : 'password');

                User::create([
                    'name' => $name,
                    'email' => $email,
                    'nisn' => $nisn ?: null,
                    'classroom_id' => $classroomId,
                    'password' => Hash::make($passToUse),
                    'role' => 'siswa',
                ]);
                $insertedCount++;
            }
        }

        $totalProcessed = $insertedCount + $updatedCount;
        $successMsg = "Proses import selesai. Berhasil menambahkan {$insertedCount} siswa baru dan memperbarui {$updatedCount} data siswa.";

        if ($totalProcessed === 0 && count($errorList) > 0) {
            return redirect()->route('admin.siswa.import')
                ->withErrors(['import_errors' => $errorList])
                ->with('error', 'Tidak ada data siswa yang berhasil diimpor. Silakan periksa pesan kendala di bawah.');
        }

        $redirect = redirect()->route('admin.siswa.index')->with('success', $successMsg);
        if (count($errorList) > 0) {
            $redirect->with('warning_list', $errorList);
        }

        return $redirect;
    }
}

