<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntiCheatExamTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private User $siswa;
    private Classroom $classroom;
    private Subject $subject;
    private Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classroom = Classroom::create(['name' => 'Kelas IX-A']);
        $this->subject = Subject::create(['name' => 'Informatika']);

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Pengawas',
            'email' => 'pengawas@cbt.id',
        ]);
        $this->guru->subjects()->attach($this->subject->id);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Budi Santoso',
            'email' => 'budi@siswa.id',
            'classroom_id' => $this->classroom->id,
            'nisn' => '1234567890',
        ]);

        $this->exam = Exam::create([
            'title' => 'Ujian Akhir Semester TIK',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'exam_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'end_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'status' => 'published',
            'token' => 'TIK2026',
        ]);

        Question::create([
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'question_text' => 'Apa kepanjangan dari CPU?',
            'option_a' => 'Central Processing Unit',
            'option_b' => 'Control Program Unit',
            'option_c' => 'Central Power Unit',
            'option_d' => 'Computer Personal Unit',
            'correct_answer' => 'A',
        ]);
    }

    /**
     * Test: Siswa yang membuka ruang ujian melihat komponen anti-cheat dan layar penuh
     */
    public function test_student_exam_room_has_fullscreen_and_anticheat_modal(): void
    {
        $response = $this->actingAs($this->siswa)
            ->withSession(["exam_token_verified_{$this->exam->id}" => true])
            ->get(route('siswa.ujian.show', $this->exam->id));

        $response->assertStatus(200);
        $response->assertSee('Wajib Mode Layar Penuh');
        $response->assertSee('PELANGGARAN TERDETEKSI!');
        $response->assertSee('TERDETEKSI CURANG');
        $response->assertSee('log-pelanggaran');
        $response->assertSee('enterFullscreen');
    }

    /**
     * Test: Ruang ujian dilengkapi penguncian tombol back bawaan HP dan gesture swipe
     */
    public function test_student_exam_room_has_mobile_back_button_and_gesture_lock(): void
    {
        $response = $this->actingAs($this->siswa)
            ->withSession(["exam_token_verified_{$this->exam->id}" => true])
            ->get(route('siswa.ujian.show', $this->exam->id));

        $response->assertStatus(200);
        $response->assertSee('lockNavigationHistory');
        $response->assertSee('preventEdgeSwipeNavigation');
        $response->assertSee('preventPageUnload');
        $response->assertSee('overscroll-behavior');
        $response->assertSee('requestWakeLock');
        $response->assertSee('Back Button Handphone');
    }

    /**
     * Test: Siswa mencatat pelanggaran via AJAX dan diincrement pada sesi
     */
    public function test_student_can_log_violation_and_increments_count(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now(),
            'status' => 'ongoing',
            'violation_count' => 0,
            'is_cheating_detected' => false,
        ]);

        $response = $this->actingAs($this->siswa)
            ->postJson(route('siswa.ujian.log_violation', $this->exam->id), [
                'reason' => 'Keluar tab browser',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'violation_count' => 1,
                'is_cheating_detected' => false,
            ]);

        $session->refresh();
        $this->assertEquals(1, $session->violation_count);
        $this->assertFalse($session->is_cheating_detected);
        $this->assertNotNull($session->last_violation_at);
    }

    /**
     * Test: Pelanggaran mencapai 4 kali menandai siswa terdeteksi curang
     */
    public function test_student_reaching_four_violations_is_flagged_as_cheating(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now(),
            'status' => 'ongoing',
            'violation_count' => 3,
            'is_cheating_detected' => false,
        ]);

        $response = $this->actingAs($this->siswa)
            ->postJson(route('siswa.ujian.log_violation', $this->exam->id), [
                'reason' => 'Membuka aplikasi lain untuk keempat kalinya',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'violation_count' => 4,
                'is_cheating_detected' => true,
            ]);

        $session->refresh();
        $this->assertEquals(4, $session->violation_count);
        $this->assertTrue($session->is_cheating_detected);
        $this->assertTrue($session->isCheating());
    }

    /**
     * Test: Halaman monitoring guru menampilkan status integritas dan mendeteksi siswa curang
     */
    public function test_guru_monitoring_displays_integrity_status_and_cheating_flag(): void
    {
        // Siswa 1: Curang (4x keluar)
        ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now(),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
        ]);

        // Siswa 2: Tertib (0x keluar)
        $siswa2 = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siti Aminah',
            'classroom_id' => $this->classroom->id,
        ]);
        ExamSession::create([
            'user_id' => $siswa2->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now(),
            'status' => 'ongoing',
            'violation_count' => 0,
            'is_cheating_detected' => false,
        ]);

        $response = $this->actingAs($this->guru)
            ->get(route('guru.monitoring.index'));

        $response->assertStatus(200);
        $response->assertSee('Terdeteksi Curang');
        $response->assertSee('Terdeteksi Curang (4x)');
        $response->assertSee('Tertib (0x)');
        $response->assertSee('Integritas Ujian');
    }

    /**
     * Test: Guru monitoring detail menampilkan banner kecurangan saat siswa >= 4x keluar
     */
    public function test_guru_monitoring_detail_shows_cheating_alert_banner(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now(),
            'status' => 'ongoing',
            'violation_count' => 5,
            'is_cheating_detected' => true,
            'last_violation_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->guru)
            ->get(route('guru.monitoring.detail', $session->id));

        $response->assertStatus(200);
        $response->assertSee('TERDETEKSI KECURANGAN: SISWA 5x KELUAR DARI UJIAN');
        $response->assertSee('STATUS: CURANG');
    }

    /**
     * Test: Filter integritas curang pada monitoring guru
     */
    public function test_guru_can_filter_monitoring_by_cheating_status(): void
    {
        // Siswa 1: Curang
        ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now(),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
        ]);

        // Siswa 2: Tertib
        $siswa2 = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Dewi Lestari',
            'classroom_id' => $this->classroom->id,
        ]);
        ExamSession::create([
            'user_id' => $siswa2->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now(),
            'status' => 'ongoing',
            'violation_count' => 0,
            'is_cheating_detected' => false,
        ]);

        // Filter hanya yang curang
        $response = $this->actingAs($this->guru)
            ->get(route('guru.monitoring.index', ['integrity' => 'curang']));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertDontSee('Dewi Lestari');
    }
}
