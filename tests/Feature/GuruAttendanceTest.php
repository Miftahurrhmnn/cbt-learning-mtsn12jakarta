<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private User $otherGuru;
    private Classroom $classA;
    private Classroom $classB;
    private Subject $subject;
    private Exam $exam;
    private User $siswaHadir;
    private User $siswaBelumHadir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classA = Classroom::create(['name' => 'Kelas VII-A']);
        $this->classB = Classroom::create(['name' => 'Kelas VII-B']);
        $this->subject = Subject::create(['name' => 'Matematika']);

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Pengajar',
            'email' => 'guru.pengajar@sekolah.id',
        ]);
        $this->guru->subjects()->attach($this->subject->id);

        $this->otherGuru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Lain',
            'email' => 'guru.lain@sekolah.id',
        ]);

        $this->exam = Exam::create([
            'title' => 'Ujian Tengah Semester MTK',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classA->id,
            'user_id' => $this->guru->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'MTK01',
        ]);
        $this->exam->classrooms()->sync([$this->classA->id]);

        // Siswa 1: Hadir (masuk ujian)
        $this->siswaHadir = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siswa Hadir Tepat Waktu',
            'nisn' => '9988776655',
            'classroom_id' => $this->classA->id,
        ]);

        // Catat sesi pengerjaan ujian siswa (jam masuk)
        ExamSession::create([
            'user_id' => $this->siswaHadir->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::create(2026, 10, 11, 8, 15, 30),
            'status' => 'ongoing',
        ]);

        // Siswa 2: Belum Masuk Ujian
        $this->siswaBelumHadir = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siswa Belum Masuk',
            'nisn' => '1122334455',
            'classroom_id' => $this->classA->id,
        ]);
    }

    public function test_guru_can_access_attendance_page_and_see_table_columns(): void
    {
        $response = $this->actingAs($this->guru)->get(route('guru.attendance.index', [
            'exam_id' => $this->exam->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Daftar Hadir Siswa Masuk Ujian');
        $response->assertSee('Nama Siswa');
        $response->assertSee('NISN');
        $response->assertSee('Kelas');
        $response->assertSee('Nama Ujian');
        $response->assertSee('Jam Masuk');
        $response->assertSee('Status');

        // Siswa hadir harus tertera jam masuk & status Hadir
        $response->assertSee('Siswa Hadir Tepat Waktu');
        $response->assertSee('9988776655');
        $response->assertSee('08:15:30 WIB');
        $response->assertSee('Hadir');

        // Siswa belum hadir harus tertera status Belum Hadir
        $response->assertSee('Siswa Belum Masuk');
        $response->assertSee('1122334455');
        $response->assertSee('Belum Hadir');
    }

    public function test_sidebar_contains_daftar_hadir_menu(): void
    {
        $response = $this->actingAs($this->guru)->get(route('guru.ujian.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Hadir');
        $response->assertSee(route('guru.attendance.index'));
    }

    public function test_guru_can_fetch_attendance_realtime_json(): void
    {
        $response = $this->actingAs($this->guru)->getJson(route('guru.attendance.data', [
            'exam_id' => $this->exam->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'stats' => [
                'total_target' => 2,
                'total_hadir' => 1,
                'total_belum_hadir' => 1,
            ],
        ]);

        $rows = $response->json('rows');
        $this->assertCount(2, $rows);

        $hadirRow = collect($rows)->firstWhere('nisn', '9988776655');
        $this->assertNotNull($hadirRow);
        $this->assertTrue($hadirRow['is_hadir']);
        $this->assertSame('08:15:30', $hadirRow['jam_masuk']);
        $this->assertSame('Hadir', $hadirRow['status_label']);

        $belumRow = collect($rows)->firstWhere('nisn', '1122334455');
        $this->assertNotNull($belumRow);
        $this->assertFalse($belumRow['is_hadir']);
        $this->assertSame('Belum Hadir', $belumRow['status_label']);
    }

    public function test_attendance_filters_by_status(): void
    {
        // Filter hadir
        $responseHadir = $this->actingAs($this->guru)->get(route('guru.attendance.index', [
            'exam_id' => $this->exam->id,
            'status' => 'hadir',
        ]));
        $responseHadir->assertSee('Siswa Hadir Tepat Waktu');
        $responseHadir->assertDontSee('Siswa Belum Masuk');

        // Filter belum hadir
        $responseBelum = $this->actingAs($this->guru)->get(route('guru.attendance.index', [
            'exam_id' => $this->exam->id,
            'status' => 'belum_hadir',
        ]));
        $responseBelum->assertSee('Siswa Belum Masuk');
        $responseBelum->assertDontSee('Siswa Hadir Tepat Waktu');
    }

    public function test_other_guru_does_not_see_another_guru_exam_attendance(): void
    {
        // Guru lain mengakses data kehadiran tanpa memilih exam_id
        $responseOther = $this->actingAs($this->otherGuru)->get(route('guru.attendance.index'));
        $responseOther->assertStatus(200);
        $responseOther->assertDontSee('Ujian Tengah Semester MTK');
    }

    public function test_students_from_multiple_different_classes_all_appear_when_filter_all_classrooms(): void
    {
        // Siswa di Kelas VII-B (kelas berbeda dari siswa 1)
        $siswaKelasB = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siswa Unik Kelas B',
            'nisn' => '7766554433',
            'classroom_id' => $this->classB->id,
        ]);

        // Buat ujian guru untuk Kelas VII-B
        $examB = Exam::create([
            'title' => 'Ujian Bahasa Kelas VII-B',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classB->id,
            'user_id' => $this->guru->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'BHS01',
        ]);
        $examB->classrooms()->sync([$this->classB->id]);

        // Rekam kehadiran Siswa Kelas B
        ExamSession::create([
            'user_id' => $siswaKelasB->id,
            'exam_id' => $examB->id,
            'start_time' => Carbon::create(2026, 10, 11, 9, 0, 0),
            'status' => 'ongoing',
        ]);

        // 1. Ketika filter Semua Kelas (tanpa classroom_id), KEDUA siswa dari KELAS BERBEDA harus muncul
        $responseAll = $this->actingAs($this->guru)->get(route('guru.attendance.index'));
        $responseAll->assertStatus(200);
        $responseAll->assertSee('Siswa Hadir Tepat Waktu'); // Siswa di Kelas VII-A
        $responseAll->assertSee('Siswa Unik Kelas B');      // Siswa di Kelas VII-B

        // Cek juga melalui endpoint data JSON realtime
        $responseJson = $this->actingAs($this->guru)->getJson(route('guru.attendance.data'));
        $responseJson->assertStatus(200);
        $rows = $responseJson->json('rows');
        $this->assertNotNull(collect($rows)->firstWhere('name', 'Siswa Hadir Tepat Waktu'));
        $this->assertNotNull(collect($rows)->firstWhere('name', 'Siswa Unik Kelas B'));

        // 2. Ketika difilter ke Kelas VII-A saja
        $responseClassA = $this->actingAs($this->guru)->get(route('guru.attendance.index', [
            'classroom_id' => $this->classA->id,
        ]));
        $responseClassA->assertSee('Siswa Hadir Tepat Waktu');
        $responseClassA->assertDontSee('Siswa Unik Kelas B');

        // 3. Ketika difilter ke Kelas VII-B saja
        $responseClassB = $this->actingAs($this->guru)->get(route('guru.attendance.index', [
            'classroom_id' => $this->classB->id,
        ]));
        $responseClassB->assertSee('Siswa Unik Kelas B');
        $responseClassB->assertDontSee('Siswa Hadir Tepat Waktu');
    }
}
