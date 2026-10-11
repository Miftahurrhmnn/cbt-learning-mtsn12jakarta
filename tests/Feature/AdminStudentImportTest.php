<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class AdminStudentImportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guru;
    private User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->guru = User::factory()->create([
            'role' => 'guru',
        ]);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
        ]);
    }

    public function test_sidebar_contains_separate_import_siswa_menu(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.siswa.index'));

        $response->assertStatus(200);
        $response->assertSee('Import Siswa (Excel/CSV)');
        $response->assertSee(route('admin.siswa.import'));
    }

    public function test_only_admin_can_access_student_import_page(): void
    {
        // Admin dapat mengakses
        $response = $this->actingAs($this->admin)->get(route('admin.siswa.import'));
        $response->assertStatus(200);
        $response->assertSee('Import Data Siswa (Excel & CSV)');
        $response->assertSee('Download Excel (.xlsx)');
        $response->assertSee('Download CSV (.csv)', false);
        $response->assertSee('Keterangan & Aturan Isi Kolom di File Excel/CSV', false);

        // Guru ditolak dan diredirect
        $this->actingAs($this->guru)
            ->get(route('admin.siswa.import'))
            ->assertRedirect(route('guru.ujian.index'));

        // Siswa ditolak dan diredirect
        $this->actingAs($this->siswa)
            ->get(route('admin.siswa.import'))
            ->assertRedirect(route('siswa.dashboard'));
    }

    public function test_admin_can_download_csv_template(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.siswa.template', 'csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="template_import_siswa.csv"');
    }

    public function test_admin_can_download_excel_template(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.siswa.template', 'xlsx'));

        $response->assertStatus(200);
        $this->assertStringContainsString('template_import_siswa.xlsx', (string) $response->headers->get('content-disposition'));
    }

    public function test_admin_can_import_students_from_csv(): void
    {
        $csvContent = "\xEF\xBB\xBF" . "Nama Lengkap,NISN,Kelas,Email,Password\n" .
            "Farel Prayoga,0081112223,Kelas 7-A,farel@siswa.mtsn12.sch.id,secret123\n" .
            "Nabila Taqiyyah,0083334445,Kelas 7-B,,\n";

        $file = UploadedFile::fake()->createWithContent('siswa.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.siswa.process-import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.siswa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Farel Prayoga',
            'nisn' => '0081112223',
            'email' => 'farel@siswa.mtsn12.sch.id',
            'role' => 'siswa',
        ]);

        // Siswa kedua tanpa email harus memiliki email otomatis dengan NISN
        $this->assertDatabaseHas('users', [
            'name' => 'Nabila Taqiyyah',
            'nisn' => '0083334445',
            'email' => '0083334445@siswa.mtsn12.sch.id',
            'role' => 'siswa',
        ]);

        // Kelas otomatis dibuat
        $this->assertDatabaseHas('classrooms', [
            'name' => 'Kelas 7-A',
        ]);
        $this->assertDatabaseHas('classrooms', [
            'name' => 'Kelas 7-B',
        ]);
    }

    public function test_admin_can_import_students_from_excel(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Nama Lengkap', 'NISN', 'Kelas', 'Email', 'Password'],
            ['Salma Salsabil', '0085556667', 'Kelas 8-C', 'salma@siswa.mtsn12.sch.id', 'pass123'],
        ]);

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_test_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $file = new UploadedFile($tempPath, 'data_siswa.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->actingAs($this->admin)->post(route('admin.siswa.process-import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.siswa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Salma Salsabil',
            'nisn' => '0085556667',
            'email' => 'salma@siswa.mtsn12.sch.id',
            'role' => 'siswa',
        ]);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_import_updates_existing_students_without_duplication(): void
    {
        $existing = User::factory()->create([
            'name' => 'Nama Lama',
            'nisn' => '0089999999',
            'email' => 'lama@siswa.mtsn12.sch.id',
            'role' => 'siswa',
        ]);

        $csvContent = "Nama Lengkap,NISN,Kelas,Email,Password\n" .
            "Nama Diperbarui,0089999999,Kelas 9-A,lama@siswa.mtsn12.sch.id,\n";

        $file = UploadedFile::fake()->createWithContent('update.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.siswa.process-import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.siswa.index'));

        // Pastikan tidak ada duplikasi
        $this->assertEquals(1, User::where('nisn', '0089999999')->count());

        $this->assertDatabaseHas('users', [
            'id' => $existing->id,
            'name' => 'Nama Diperbarui',
            'nisn' => '0089999999',
        ]);
    }
}
