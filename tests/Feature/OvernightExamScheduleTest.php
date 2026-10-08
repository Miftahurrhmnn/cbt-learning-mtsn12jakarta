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

class OvernightExamScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private User $siswa;
    private Classroom $classroom;
    private Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classroom = Classroom::create(['name' => 'Kelas VIII-A']);
        $this->subject = Subject::create(['name' => 'Fisika']);

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru IPA',
            'email' => 'guruipa@cbt.id',
        ]);
        $this->guru->subjects()->attach($this->subject->id);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Rizky Pratama',
            'email' => 'rizky@siswa.id',
            'classroom_id' => $this->classroom->id,
            'nisn' => '9988776655',
        ]);
    }

    /**
     * Test: Durasi otomatis dihitung 360 menit (6 jam) saat guru membuat ujian 23:00 - 05:00
     */
    public function test_guru_creates_overnight_exam_calculates_correct_duration(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => 'Ujian Malam Lintas Hari',
            'subject_id' => $this->subject->id,
            'classroom_ids' => [$this->classroom->id],
            'status' => 'published',
            'exam_date' => '2026-10-08',
            'start_time' => '23:00',
            'end_time' => '05:00',
            'token' => 'MLM2026',
        ]);

        $exam = Exam::where('title', 'Ujian Malam Lintas Hari')->first();
        $this->assertNotNull($exam);
        // 23:00 ke 05:00 besok pagi = 6 jam = 360 menit
        $this->assertEquals(360, $exam->duration);
        $this->assertEquals('23:00 - 05:00 WIB', $exam->formatted_time_range);
    }

    /**
     * Test: Status ujian 23:00 - 05:00 pada jam 23:25 TIDAK berakhir dan siswa dapat mulai ujian
     */
    public function test_overnight_exam_is_active_at_23_25_and_not_ended(): void
    {
        // Set waktu sekarang ke 2026-10-08 23:25:00 WIB
        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d H:i:s', '2026-10-08 23:25:00', 'Asia/Jakarta'));

        $exam = Exam::create([
            'title' => 'Simulasi Ujian Malam',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'exam_date' => '2026-10-08',
            'start_time' => '23:00',
            'end_time' => '05:00',
            'duration' => 360,
            'status' => 'published',
            'token' => 'MLM2026',
        ]);
        $exam->classrooms()->sync([$this->classroom->id]);

        Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $this->subject->id,
            'question_text' => 'Soal Fisika 1',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_answer' => 'A',
        ]);

        // Verifikasi method model
        $this->assertFalse($exam->hasNotStartedYet());
        $this->assertFalse($exam->hasEnded());
        $this->assertTrue($exam->isWithinTimeWindow());

        // 1. Siswa cek dashboard: tombol harus "Mulai Ujian (Token)", BUKAN "Waktu Ujian Berakhir"
        $responseDashboard = $this->actingAs($this->siswa)->get(route('siswa.dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('Mulai Ujian (Token)');
        $responseDashboard->assertDontSee('Waktu Ujian Berakhir');

        // 2. Siswa buka halaman token: form token aktif dan tidak disabled
        $responseToken = $this->actingAs($this->siswa)->get(route('siswa.ujian.token', $exam->id));
        $responseToken->assertStatus(200);
        $responseToken->assertSee('Mulai Ujian');
        $responseToken->assertDontSee('Waktu Ujian Telah Berakhir');
        $responseToken->assertDontSee('Peringatan: Ujian Belum Dimulai!');

        // 3. Siswa verifikasi token: berhasil redirect ke ruang ujian
        $responseVerify = $this->actingAs($this->siswa)->post(route('siswa.ujian.verify-token', $exam->id), [
            'token' => 'MLM2026',
        ]);
        $responseVerify->assertRedirect(route('siswa.ujian.show', $exam->id));

        // 4. Siswa masuk ke ruang ujian: berhasil masuk dan sisa waktu aktif
        $responseRoom = $this->actingAs($this->siswa)->get(route('siswa.ujian.show', $exam->id));
        $responseRoom->assertStatus(200);
        $responseRoom->assertSee('23:00 - 05:00 WIB');

        Carbon::setTestNow(); // Reset mock time
    }

    /**
     * Test: Status ujian 23:00 - 05:00 pada jam 02:00 dini hari (hari berikutnya) masih aktif
     */
    public function test_overnight_exam_is_still_active_next_day_at_02_00(): void
    {
        // Set waktu sekarang ke 2026-10-09 02:00:00 WIB (dini hari berikutnya)
        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d H:i:s', '2026-10-09 02:00:00', 'Asia/Jakarta'));

        $exam = Exam::create([
            'title' => 'Ujian Dini Hari',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'exam_date' => '2026-10-08',
            'start_time' => '23:00',
            'end_time' => '05:00',
            'duration' => 360,
            'status' => 'published',
            'token' => 'DNH2026',
        ]);
        $exam->classrooms()->sync([$this->classroom->id]);

        $this->assertFalse($exam->hasNotStartedYet());
        $this->assertFalse($exam->hasEnded());
        $this->assertTrue($exam->isWithinTimeWindow());

        Carbon::setTestNow();
    }

    /**
     * Test: Status ujian 23:00 - 05:00 pada jam 05:05 (lewat batas selesai) menjadi berakhir
     */
    public function test_overnight_exam_has_ended_after_05_00(): void
    {
        // Set waktu sekarang ke 2026-10-09 05:05:00 WIB
        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d H:i:s', '2026-10-09 05:05:00', 'Asia/Jakarta'));

        $exam = Exam::create([
            'title' => 'Ujian Selesai Pagi',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'exam_date' => '2026-10-08',
            'start_time' => '23:00',
            'end_time' => '05:00',
            'duration' => 360,
            'status' => 'published',
            'token' => 'SLS2026',
        ]);
        $exam->classrooms()->sync([$this->classroom->id]);

        $this->assertFalse($exam->hasNotStartedYet());
        $this->assertTrue($exam->hasEnded());
        $this->assertFalse($exam->isWithinTimeWindow());

        // Token page shows ended
        $responseToken = $this->actingAs($this->siswa)->get(route('siswa.ujian.token', $exam->id));
        $responseToken->assertStatus(200);
        $responseToken->assertSee('Waktu Ujian Telah Berakhir');

        Carbon::setTestNow();
    }

    /**
     * Test: Status ujian 23:00 - 05:00 sebelum jam 23:00 (misal jam 22:00) berstatus Belum Dimulai
     */
    public function test_overnight_exam_has_not_started_before_23_00(): void
    {
        // Set waktu sekarang ke 2026-10-08 22:00:00 WIB
        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d H:i:s', '2026-10-08 22:00:00', 'Asia/Jakarta'));

        $exam = Exam::create([
            'title' => 'Ujian Belum Mulai Malam',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'exam_date' => '2026-10-08',
            'start_time' => '23:00',
            'end_time' => '05:00',
            'duration' => 360,
            'status' => 'published',
            'token' => 'BLM2026',
        ]);
        $exam->classrooms()->sync([$this->classroom->id]);

        $this->assertTrue($exam->hasNotStartedYet());
        $this->assertFalse($exam->hasEnded());
        $this->assertFalse($exam->isWithinTimeWindow());

        Carbon::setTestNow();
    }
}
