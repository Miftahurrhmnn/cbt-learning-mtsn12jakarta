<?php

namespace Tests\Feature;

use App\Jobs\ProcessStudentAnswerJob;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class HighConcurrencyOptimizationTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private User $siswa;
    private Classroom $classroom;
    private Subject $subject;
    private Exam $exam;
    private Question $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classroom = Classroom::create(['name' => 'Kelas X-A']);
        $this->subject = Subject::create(['name' => 'Fisika']);

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Fisika',
            'email' => 'fisika@cbt.id',
        ]);
        $this->guru->subjects()->attach($this->subject->id);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Budi High Concurrency',
            'classroom_id' => $this->classroom->id,
            'nisn' => '1122334455',
        ]);

        $this->exam = Exam::create([
            'title' => 'Ujian Fisika Skala 500 User',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'FSK500',
        ]);

        $this->question = Question::create([
            'exam_id' => $this->exam->id,
            'subject_id' => $this->subject->id,
            'question_text' => 'Satuan SI untuk gaya adalah?',
            'option_a' => 'Joule',
            'option_b' => 'Newton',
            'option_c' => 'Watt',
            'option_d' => 'Pascal',
            'correct_answer' => 'B',
        ]);
    }

    /**
     * Test: Butir soal publik dicache sehingga 500 siswa tidak melakukan query berulang
     */
    public function test_exam_questions_are_cached_on_room_access(): void
    {
        $cacheKey = "exam_questions_public_{$this->exam->id}";
        Cache::forget($cacheKey);
        $this->assertFalse(Cache::has($cacheKey));

        // Request 1: Mengisi cache
        $response1 = $this->actingAs($this->siswa)
            ->withSession(["exam_token_verified_{$this->exam->id}" => true])
            ->get(route('siswa.ujian.show', $this->exam->id));

        $response1->assertStatus(200);
        $this->assertTrue(Cache::has($cacheKey));

        // Request 2: Dilayani langsung dari cache
        $cachedQuestions = Cache::get($cacheKey);
        $this->assertCount(1, $cachedQuestions);
        $this->assertEquals('Satuan SI untuk gaya adalah?', $cachedQuestions->first()->question_text);
    }

    /**
     * Test: Simpan jawaban memanfaatkan in-memory map & upsert cepat
     */
    public function test_save_answer_uses_cached_map_and_upserts_answer(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
        ]);

        $response = $this->actingAs($this->siswa)
            ->postJson(route('siswa.ujian.simpan_jawaban', $this->exam->id), [
                'question_id' => $this->question->id,
                'selected_answer' => 'B',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'question_id' => $this->question->id,
                'selected_answer' => 'B',
            ]);

        $this->assertDatabaseHas('exam_answers', [
            'exam_session_id' => $session->id,
            'question_id' => $this->question->id,
            'selected_answer' => 'B',
            'is_correct' => 1,
        ]);

        // Cek bahwa cache map butir soal terbentuk
        $this->assertTrue(Cache::has("exam_questions_map_{$this->exam->id}"));
    }

    /**
     * Test: ProcessStudentAnswerJob berhasil mengeksekusi upsert jawaban
     */
    public function test_process_student_answer_job_executes_upsert(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->siswa->id,
            'exam_id' => $this->exam->id,
            'start_time' => Carbon::now('Asia/Jakarta'),
            'status' => 'ongoing',
        ]);

        $job = new ProcessStudentAnswerJob(
            $session->id,
            $this->question->id,
            'B',
            true
        );

        $job->handle();

        $this->assertDatabaseHas('exam_answers', [
            'exam_session_id' => $session->id,
            'question_id' => $this->question->id,
            'selected_answer' => 'B',
            'is_correct' => 1,
        ]);
    }

    /**
     * Test: Siswa dashboard mencache ujian aktif untuk mengurangi beban server
     */
    public function test_siswa_dashboard_caches_active_exams(): void
    {
        $userClassId = (int)$this->siswa->classroom_id;
        $cacheKey = "siswa_dashboard_exams_c{$userClassId}_dall_snone";
        Cache::forget($cacheKey);

        $response = $this->actingAs($this->siswa)->get(route('siswa.dashboard'));
        $response->assertStatus(200);

        $this->assertTrue(Cache::has($cacheKey));
    }

    /**
     * Test: Saat Guru menambah soal baru, cache ujian otomatis direset
     */
    public function test_guru_adding_question_clears_exam_cache(): void
    {
        $pubKey = "exam_questions_public_{$this->exam->id}";
        $mapKey = "exam_questions_map_{$this->exam->id}";
        Cache::put($pubKey, 'dummy', 600);
        Cache::put($mapKey, 'dummy', 600);

        $this->assertTrue(Cache::has($pubKey));
        $this->assertTrue(Cache::has($mapKey));

        $this->actingAs($this->guru)
            ->post(route('guru.ujian.soal.store', $this->exam->id), [
                'question_text' => 'Soal baru kedua',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_answer' => 'A',
            ]);

        $this->assertFalse(Cache::has($pubKey));
        $this->assertFalse(Cache::has($mapKey));
    }

    /**
     * Test: Data polling daftar hadir dicache sejenak untuk menahan rapid polling
     */
    public function test_guru_attendance_data_is_cached_against_rapid_polling(): void
    {
        $response1 = $this->actingAs($this->guru)
            ->getJson(route('guru.attendance.data'));

        $response1->assertStatus(200)
            ->assertJson(['success' => true]);

        $cacheKey = "att_g_{$this->guru->id}_e_all_c_all_s_" . md5('') . "_st_all";
        $this->assertTrue(Cache::has($cacheKey));
    }
}
