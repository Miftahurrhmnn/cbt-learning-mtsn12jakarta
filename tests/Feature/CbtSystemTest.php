<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CbtSystemTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_guru_and_siswa_role_access_and_cross_redirect(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Guru can access guru dashboard
        $response = $this->actingAs($guru)->get(route('guru.ujian.index'));
        $response->assertStatus(200);

        // Siswa accessing guru page is directly redirected to siswa dashboard
        $response = $this->actingAs($siswa)->get(route('guru.ujian.index'));
        $response->assertRedirect(route('siswa.dashboard'));

        // Siswa can access siswa dashboard
        $response = $this->actingAs($siswa)->get(route('siswa.dashboard'));
        $response->assertStatus(200);

        // Guru accessing siswa page is directly redirected to guru index
        $response = $this->actingAs($guru)->get(route('siswa.dashboard'));
        $response->assertRedirect(route('guru.ujian.index'));
    }

    public function test_login_redirects_explicitly_by_role(): void
    {
        $guru = User::factory()->create([
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $siswa = User::factory()->create([
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        // Login as Guru must redirect to guru.ujian.index
        $responseGuru = $this->post('/login', [
            'email' => $guru->email,
            'password' => 'password',
        ]);
        $responseGuru->assertRedirect(route('guru.ujian.index'));

        // Logout
        $this->post('/logout');

        // Login as Siswa must redirect to siswa.dashboard
        $responseSiswa = $this->post('/login', [
            'email' => $siswa->email,
            'password' => 'password',
        ]);
        $responseSiswa->assertRedirect(route('siswa.dashboard'));
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_anti_cheat_hides_correct_answer_from_siswa(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $subject = Subject::create(['name' => 'Biologi']);
        $classroom = Classroom::create(['name' => 'Kelas XII']);

        $exam = Exam::create([
            'title' => 'Ujian Biologi',
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'duration' => 60,
            'status' => 'published',
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'question_text' => 'Apa itu fotosintesis?',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_answer' => 'A',
        ]);

        $response = $this->actingAs($siswa)->get(route('siswa.ujian.show', $exam->id));
        $response->assertStatus(200);

        // Verify that correct_answer key is NOT in the view questions data
        $questions = $response->viewData('questions');
        foreach ($questions as $q) {
            $this->assertArrayNotHasKey('correct_answer', $q->toArray());
        }
    }

    public function test_guru_can_create_exam_with_subject_and_classroom_and_toggle_status(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::create(['name' => 'Matematika']);
        $classroom = Classroom::create(['name' => 'Kelas X-A']);

        // Create exam
        $response = $this->actingAs($guru)->post(route('guru.ujian.store'), [
            'title' => 'Ujian Aljabar Unik',
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'duration' => 60,
            'status' => 'draft',
        ]);

        $exam = Exam::where('title', 'Ujian Aljabar Unik')->first();
        $this->assertNotNull($exam);
        $this->assertEquals('draft', $exam->status);
        $this->assertEquals(60, $exam->duration);
        $response->assertRedirect(route('guru.ujian.show', $exam->id));

        // Add a question with image
        $image = UploadedFile::fake()->image('diagram.jpg', 600, 400);
        $this->actingAs($guru)->post(route('guru.ujian.soal.store', $exam->id), [
            'question_text' => 'Berapa 10 + 20?',
            'image' => $image,
            'option_a' => '25',
            'option_b' => '30',
            'option_c' => '35',
            'option_d' => '40',
            'correct_answer' => 'B',
        ]);

        $question = Question::where('exam_id', $exam->id)->first();
        $this->assertNotNull($question);
        $this->assertEquals('B', $question->correct_answer);
        $this->assertNotNull($question->image);
        Storage::disk('public')->assertExists($question->image);

        // Guru starts exam (toggle status)
        $this->actingAs($guru)->post(route('guru.ujian.toggle_status', $exam->id));
        $exam->refresh();
        $this->assertEquals('published', $exam->status);
    }

    public function test_siswa_exam_session_ajax_answer_and_conditional_scoring(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $subject = Subject::create(['name' => 'Fisika']);
        $classroom = Classroom::create(['name' => 'Kelas XI']);

        $exam = Exam::create([
            'title' => 'Ujian Fisika Dasar',
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'duration' => 60,
            'status' => 'published',
        ]);

        $q1 = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'question_text' => 'Soal 1',
            'option_a' => 'Opsi A',
            'option_b' => 'Opsi B',
            'option_c' => 'Opsi C',
            'option_d' => 'Opsi D',
            'correct_answer' => 'A',
        ]);

        $q2 = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'question_text' => 'Soal 2',
            'option_a' => 'Opsi A',
            'option_b' => 'Opsi B',
            'option_c' => 'Opsi C',
            'option_d' => 'Opsi D',
            'correct_answer' => 'C',
        ]);

        // Siswa enters exam room
        $response = $this->actingAs($siswa)->get(route('siswa.ujian.show', $exam->id));
        $response->assertStatus(200);

        $session = ExamSession::where('user_id', $siswa->id)->where('exam_id', $exam->id)->first();
        $this->assertNotNull($session);
        $this->assertEquals('ongoing', $session->status);
        $this->assertNull($session->score);

        // Check that result page does NOT display score while ongoing
        $resultResponse = $this->actingAs($siswa)->get(route('siswa.ujian.hasil', $exam->id));
        $resultResponse->assertSee('Ujian Belum Selesai');
        $resultResponse->assertDontSee('Nilai Akhir');

        // Check that Guru scores page also shows "Nilai Belum Keluar"
        $guruScoresResponse = $this->actingAs($guru)->get(route('guru.ujian.scores', $exam->id));
        $guruScoresResponse->assertSee('Nilai Belum Keluar');

        // Siswa answers Q1 correctly with 'A' via AJAX
        $ajaxResponse = $this->actingAs($siswa)->postJson(route('siswa.ujian.simpan_jawaban', $exam->id), [
            'question_id' => $q1->id,
            'selected_answer' => 'A',
        ]);
        $ajaxResponse->assertStatus(200)->assertJson(['success' => true]);

        // Siswa answers Q2 incorrectly with 'B' via AJAX
        $ajaxResponse2 = $this->actingAs($siswa)->postJson(route('siswa.ujian.simpan_jawaban', $exam->id), [
            'question_id' => $q2->id,
            'selected_answer' => 'B',
        ]);
        $ajaxResponse2->assertStatus(200)->assertJson(['success' => true]);

        // Siswa finishes exam
        $finishResponse = $this->actingAs($siswa)->post(route('siswa.ujian.selesai', $exam->id));
        $finishResponse->assertRedirect(route('siswa.ujian.hasil', $exam->id));

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals(1, $session->correct_answers);
        $this->assertEquals(2, $session->total_questions);
        $this->assertEquals(50.00, (float) $session->score);

        // Now Siswa CAN see the score!
        $finalSiswaResult = $this->actingAs($siswa)->get(route('siswa.ujian.hasil', $exam->id));
        $finalSiswaResult->assertSee('50.0');
        $finalSiswaResult->assertSee('Nilai Akhir');

        // Guru CAN now see the score!
        $finalGuruScores = $this->actingAs($guru)->get(route('guru.ujian.scores', $exam->id));
        $finalGuruScores->assertSee('50.0');
        $finalGuruScores->assertSee('Selesai');
    }
}
