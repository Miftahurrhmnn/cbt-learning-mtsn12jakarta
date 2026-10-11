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

class StudentExamLockAntiCheatTest extends TestCase
{
    use RefreshDatabase;

    private User $guruCreator;
    private User $otherGuru;
    private User $siswa;
    private Classroom $classroom;
    private Subject $subject;
    private Exam $exam;
    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classroom = Classroom::create(['name' => 'Kelas IX-A']);
        $this->subject = Subject::create(['name' => 'Matematika']);

        $this->guruCreator = User::factory()->create([
            'role' => 'guru',
            'name' => 'Ibu Seftiharneli',
            'email' => 'seftiharneli@teacher.mtsn12.sch.id',
        ]);
        $this->guruCreator->subjects()->attach($this->subject->id);

        $this->otherGuru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Lain',
            'email' => 'otherguru@teacher.mtsn12.sch.id',
        ]);
        $this->otherGuru->subjects()->attach($this->subject->id);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Ahmad Siswa',
            'email' => 'ahmad@siswa.mtsn12.sch.id',
            'classroom_id' => $this->classroom->id,
            'nisn' => '9988776655',
        ]);

        $this->exam = Exam::create([
            'title' => 'Ujian Akhir Matematika IX',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guruCreator->id,
            'exam_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'end_date' => Carbon::now('Asia/Jakarta')->toDateString(),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'duration' => 60,
            'status' => 'published',
            'token' => 'MATH123',
        ]);

        $this->question = Question::create([
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'question_text' => 'Berapakah 2 + 2?',
            'option_a' => '3',
            'option_b' => '4',
            'option_c' => '5',
            'option_d' => '6',
            'correct_answer' => 'B',
        ]);
    }

    /**
     * Test: Pelanggaran 1-3 kali belum mengunci ujian
     */
    public function test_violations_under_four_do_not_lock_session(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 0,
            'is_cheating_detected' => false,
            'is_locked' => false,
        ]);

        // Pelanggaran 1, 2, 3
        for ($i = 1; $i <= 3; $i++) {
            $response = $this->actingAs($this->siswa)
                ->postJson(route('siswa.ujian.log_violation', $this->exam->id), [
                    'reason' => "Percobaan ke-{$i} keluar tab browser",
                ]);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'violation_count' => $i,
                    'is_cheating_detected' => false,
                    'is_locked' => false,
                ]);
        }

        $session->refresh();
        $this->assertEquals(3, $session->violation_count);
        $this->assertFalse($session->is_cheating_detected);
        $this->assertFalse($session->is_locked);
    }

    /**
     * Test: Pelanggaran ke-4 otomatis mengunci sesi ujian (is_locked = true)
     */
    public function test_fourth_violation_locks_exam_session(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 3,
            'is_cheating_detected' => false,
            'is_locked' => false,
        ]);

        $response = $this->actingAs($this->siswa)
            ->postJson(route('siswa.ujian.log_violation', $this->exam->id), [
                'reason' => 'Percobaan ke-4 keluar tab browser',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'violation_count' => 4,
                'is_cheating_detected' => true,
                'is_locked' => true,
            ]);

        $session->refresh();
        $this->assertEquals(4, $session->violation_count);
        $this->assertTrue($session->is_cheating_detected);
        $this->assertTrue($session->is_locked);
        $this->assertNotNull($session->locked_at);
        $this->assertTrue($session->isLocked());
    }

    /**
     * Test: Siswa yang terkunci tidak bisa menyimpan jawaban (ditolak dengan 403 Forbidden)
     */
    public function test_locked_student_cannot_save_answer(): void
    {
        ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
            'is_locked' => true,
            'locked_at' => Carbon::now('Asia/Jakarta'),
        ]);

        $response = $this->actingAs($this->siswa)
            ->postJson(route('siswa.ujian.simpan_jawaban', $this->exam->id), [
                'question_id' => $this->question->id,
                'selected_answer' => 'B',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'error' => 'Ujian Anda telah TERKUNCI karena terdeteksi keluar dari layar ujian sebanyak 4 kali. Silakan hubungi Guru pembuat ujian untuk membuka akses.',
                'is_locked' => true,
            ]);

        $this->assertDatabaseMissing('exam_answers', [
            'question_id' => $this->question->id,
            'selected_answer' => 'B',
        ]);
    }

    /**
     * Test: Siswa yang terkunci tidak dapat menyelesaikan ujian sendiri sebelum dibuka kuncinya
     */
    public function test_locked_student_cannot_finish_exam(): void
    {
        ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
            'is_locked' => true,
            'locked_at' => Carbon::now('Asia/Jakarta'),
        ]);

        $response = $this->actingAs($this->siswa)
            ->post(route('siswa.ujian.selesai', $this->exam->id));

        $response->assertRedirect(route('siswa.ujian.show', $this->exam->id));
        $response->assertSessionHas('error', 'Ujian Anda sedang TERKUNCI karena terdeteksi keluar dari layar ujian sebanyak 4 kali. Hubungi guru pembuat ujian untuk membuka kunci.');
    }

    /**
     * Test: Halaman ruang ujian siswa menampilkan modal terkunci & informasi guru pembuat ujian
     */
    public function test_exam_room_renders_locked_state_and_teacher_info(): void
    {
        ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
            'is_locked' => true,
            'locked_at' => Carbon::now('Asia/Jakarta'),
        ]);

        $response = $this->actingAs($this->siswa)
            ->withSession(["exam_token_verified_{$this->exam->id}" => true])
            ->get(route('siswa.ujian.show', $this->exam->id));

        $response->assertStatus(200);
        $response->assertSee('AKSES UJIAN ANDA TERKUNCI!');
        $response->assertSee('Ibu Seftiharneli');
        $response->assertSee('seftiharneli@teacher.mtsn12.sch.id');
        $response->assertSee('WAKTU UJIAN TETAP BERJALAN');
        $response->assertSee('status-kunci');
    }

    /**
     * Test: Endpoint check_lock_status mengembalikan status kunci dan sisa waktu ujian
     */
    public function test_student_can_check_lock_status_endpoint(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
            'is_locked' => true,
        ]);

        $response = $this->actingAs($this->siswa)
            ->getJson(route('siswa.ujian.check_lock_status', $this->exam->id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_locked' => true,
                'violation_count' => 4,
            ]);

        $this->assertArrayHasKey('remaining_seconds', $response->json());
    }

    /**
     * Test: Guru pembuat ujian berhasil membuka kunci akses ujian siswa
     */
    public function test_creator_teacher_can_unlock_student_session(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
            'is_locked' => true,
            'locked_at' => Carbon::now('Asia/Jakarta'),
        ]);

        $response = $this->actingAs($this->guruCreator)
            ->post(route('guru.monitoring.unlock', $session->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $session->refresh();
        $this->assertFalse($session->is_locked);
        $this->assertEquals(0, $session->violation_count);
        $this->assertFalse($session->is_cheating_detected);
        $this->assertEquals($this->guruCreator->id, $session->unlocked_by);
        $this->assertNotNull($session->unlocked_at);
        $this->assertFalse($session->isLocked());
    }

    /**
     * Test: Guru lain yang bukan pembuat ujian tidak memiliki wewenang membuka kunci (403 Forbidden)
     */
    public function test_other_teacher_cannot_unlock_student_session(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
            'is_locked' => true,
            'locked_at' => Carbon::now('Asia/Jakarta'),
        ]);

        $response = $this->actingAs($this->otherGuru)
            ->post(route('guru.monitoring.unlock', $session->id));

        $response->assertStatus(403);

        $session->refresh();
        $this->assertTrue($session->is_locked);
    }

    /**
     * Test: Siswa yang kuncinya telah dibuka oleh Guru dapat kembali menyimpan jawaban
     */
    public function test_unlocked_student_can_resume_saving_answers(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
            'violation_count' => 4,
            'is_cheating_detected' => true,
            'is_locked' => true,
            'locked_at' => Carbon::now('Asia/Jakarta'),
        ]);

        // Guru pembuat ujian membuka kunci
        $session->unlock($this->guruCreator->id);
        $this->assertFalse($session->fresh()->isLocked());

        // Siswa menjawab soal
        $response = $this->actingAs($this->siswa)
            ->postJson(route('siswa.ujian.simpan_jawaban', $this->exam->id), [
                'question_id' => $this->question->id,
                'selected_answer' => 'B',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('exam_answers', [
            'exam_session_id' => $session->id,
            'question_id' => $this->question->id,
            'selected_answer' => 'B',
        ]);
    }
}
