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
        $subject = Subject::create(['name' => 'Biologi']);
        $classroom = Classroom::create(['name' => 'Kelas XII']);
        $siswa = User::factory()->create([
            'role' => 'siswa',
            'classroom_id' => $classroom->id,
        ]);

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
        $subject = Subject::create(['name' => 'Fisika']);
        $classroom = Classroom::create(['name' => 'Kelas XI']);
        $siswa = User::factory()->create([
            'role' => 'siswa',
            'classroom_id' => $classroom->id,
        ]);

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
        // Fitur Baru: Siswa dapat melihat review jawaban dan kunci jawaban setelah selesai
        $finalSiswaResult->assertSee('Kunci Jawaban:');
        $finalSiswaResult->assertSee('Pilihan Anda:');

        // Guru CAN now see the score!
        $finalGuruScores = $this->actingAs($guru)->get(route('guru.ujian.scores', $exam->id));
        $finalGuruScores->assertSee('50.0');
        $finalGuruScores->assertSee('Selesai');
    }

    public function test_siswa_cannot_finish_exam_if_questions_remain_unanswered(): void
    {
        $classroom = Classroom::create(['name' => 'Kelas X-B']);
        $subject = Subject::create(['name' => 'Fisika']);
        $siswa = User::factory()->create([
            'role' => 'siswa',
            'classroom_id' => $classroom->id,
        ]);

        $exam = Exam::create([
            'title' => 'Ujian Fisika Dasar',
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'FSK123',
        ]);

        $q1 = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'question_text' => 'Soal 1 Fisika',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_answer' => 'A',
        ]);

        $q2 = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'question_text' => 'Soal 2 Fisika',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_answer' => 'B',
        ]);

        // Siswa starts exam
        $this->actingAs($siswa)->withSession(["exam_token_verified_{$exam->id}" => true])
            ->get(route('siswa.ujian.show', $exam->id));

        // Siswa only answers Q1
        $this->actingAs($siswa)->postJson(route('siswa.ujian.simpan_jawaban', $exam->id), [
            'question_id' => $q1->id,
            'selected_answer' => 'A',
        ]);

        // Try to finish without answering Q2
        $finishResponse = $this->actingAs($siswa)->post(route('siswa.ujian.selesai', $exam->id));
        
        // Should redirect back with error warning
        $finishResponse->assertRedirect(route('siswa.ujian.show', $exam->id));
        $finishResponse->assertSessionHas('error');

        $session = ExamSession::where('user_id', $siswa->id)->where('exam_id', $exam->id)->first();
        $this->assertEquals('ongoing', $session->status);
    }

    public function test_only_admin_can_manage_student_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $classroom = Classroom::create(['name' => 'Kelas X-C']);

        // Guru cannot access admin student index
        $guruResponse = $this->actingAs($guru)->get(route('admin.siswa.index'));
        $guruResponse->assertRedirect(route('guru.ujian.index'));

        // Siswa cannot access admin student index
        $siswaResponse = $this->actingAs($siswa)->get(route('admin.siswa.index'));
        $siswaResponse->assertRedirect(route('siswa.dashboard'));

        // Admin can access admin student index
        $adminResponse = $this->actingAs($admin)->get(route('admin.siswa.index'));
        $adminResponse->assertStatus(200);

        // Admin can create a new student
        $createResponse = $this->actingAs($admin)->post(route('admin.siswa.store'), [
            'name' => 'Budi Santoso Admin Input',
            'email' => 'budi.santoso@cbt.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'nisn' => '1234567890',
            'classroom_id' => $classroom->id,
        ]);

        $createResponse->assertRedirect(route('admin.siswa.index'));
        $createdStudent = User::where('email', 'budi.santoso@cbt.test')->first();
        $this->assertNotNull($createdStudent);
        $this->assertEquals('siswa', $createdStudent->role);
        $this->assertEquals('1234567890', $createdStudent->nisn);
        $this->assertEquals($classroom->id, $createdStudent->classroom_id);
    }

    public function test_siswa_only_sees_and_can_access_exams_matching_their_classroom(): void
    {
        $classXA = Classroom::create(['name' => 'Kelas X-A']);
        $classXIA = Classroom::create(['name' => 'Kelas XI-A']);
        $subject = Subject::create(['name' => 'Biologi']);

        // Siswa in Kelas XI-A
        $siswaXIA = User::factory()->create([
            'role' => 'siswa',
            'classroom_id' => $classXIA->id,
        ]);

        // Exam for Kelas X-A
        $examXA = Exam::create([
            'title' => 'Ujian Biologi Kelas X-A',
            'subject_id' => $subject->id,
            'classroom_id' => $classXA->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'BIO001',
        ]);

        // Exam for Kelas XI-A
        $examXIA = Exam::create([
            'title' => 'Ujian Biologi Kelas XI-A',
            'subject_id' => $subject->id,
            'classroom_id' => $classXIA->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'BIO002',
        ]);

        // 1. Dashboard filter test: Siswa XI-A can see exam XI-A but NOT exam X-A
        $dashboardResponse = $this->actingAs($siswaXIA)->get(route('siswa.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Ujian Biologi Kelas XI-A');
        $dashboardResponse->assertDontSee('Ujian Biologi Kelas X-A');

        // 2. Direct room URL access test: Siswa XI-A cannot access Exam X-A
        $roomResponse = $this->actingAs($siswaXIA)->get(route('siswa.ujian.show', $examXA->id));
        $roomResponse->assertRedirect(route('siswa.dashboard'));
        $roomResponse->assertSessionHas('error');

        // 3. Direct token URL access test: Siswa XI-A cannot access Exam X-A token page
        $tokenResponse = $this->actingAs($siswaXIA)->get(route('siswa.ujian.token', $examXA->id));
        $tokenResponse->assertRedirect(route('siswa.dashboard'));
        $tokenResponse->assertSessionHas('error');
    }

    public function test_guru_can_filter_exams_by_classroom_and_day(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $classXA = Classroom::create(['name' => 'Kelas X-A']);
        $classXB = Classroom::create(['name' => 'Kelas X-B']);
        $subject = Subject::create(['name' => 'Kimia']);

        $examSenin = Exam::create([
            'title' => 'Kimia Hari Senin X-A',
            'subject_id' => $subject->id,
            'classroom_id' => $classXA->id,
            'user_id' => $guru->id,
            'duration' => 60,
            'status' => 'published',
            'day_of_week' => 'Senin',
        ]);

        $examSelasa = Exam::create([
            'title' => 'Kimia Hari Selasa X-B',
            'subject_id' => $subject->id,
            'classroom_id' => $classXB->id,
            'user_id' => $guru->id,
            'duration' => 60,
            'status' => 'published',
            'day_of_week' => 'Selasa',
        ]);

        // Filter by classroom X-A
        $responseClass = $this->actingAs($guru)->get(route('guru.ujian.index', ['classroom_id' => $classXA->id]));
        $responseClass->assertStatus(200);
        $responseClass->assertSee('Kimia Hari Senin X-A');
        $responseClass->assertDontSee('Kimia Hari Selasa X-B');

        // Filter by day Selasa
        $responseDay = $this->actingAs($guru)->get(route('guru.ujian.index', ['day' => 'Selasa']));
        $responseDay->assertStatus(200);
        $responseDay->assertSee('Kimia Hari Selasa X-B');
        $responseDay->assertDontSee('Kimia Hari Senin X-A');
    }

    public function test_siswa_can_filter_exams_by_day(): void
    {
        $class = Classroom::create(['name' => 'Kelas X-C']);
        $siswa = User::factory()->create([
            'role' => 'siswa',
            'classroom_id' => $class->id,
        ]);
        $sub1 = Subject::create(['name' => 'Matematika']);
        $sub2 = Subject::create(['name' => 'Bahasa Inggris']);

        $examRabu = Exam::create([
            'title' => 'Ujian Rabu Matematika',
            'subject_id' => $sub1->id,
            'classroom_id' => $class->id,
            'duration' => 60,
            'status' => 'published',
            'day_of_week' => 'Rabu',
        ]);

        $examKamis = Exam::create([
            'title' => 'Ujian Kamis Bahasa Inggris',
            'subject_id' => $sub2->id,
            'classroom_id' => $class->id,
            'duration' => 60,
            'status' => 'published',
            'day_of_week' => 'Kamis',
        ]);

        // Siswa filters by day Rabu
        $responseRabu = $this->actingAs($siswa)->get(route('siswa.dashboard', ['day' => 'Rabu']));
        $responseRabu->assertStatus(200);
        $responseRabu->assertSee('Ujian Rabu Matematika');
        $responseRabu->assertDontSee('Ujian Kamis Bahasa Inggris');
    }

    public function test_guru_sidebar_renders_and_switches_active_menu(): void
    {
        $guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Bapak Budi, S.Pd.',
        ]);

        // When on index, Daftar Ujian is active and Buat Ujian Baru is inactive
        $responseIndex = $this->actingAs($guru)->get(route('guru.ujian.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('CBT MTsN 12 Jakarta');
        $responseIndex->assertSee('Daftar Ujian');
        $responseIndex->assertSee('Buat Ujian Baru');
        $responseIndex->assertSee('Guru Aktif');
        $responseIndex->assertSee('Bapak Budi, S.Pd.');

        // When on create, Buat Ujian Baru is active
        $responseCreate = $this->actingAs($guru)->get(route('guru.ujian.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('CBT MTsN 12 Jakarta');
        $responseCreate->assertSee('Formulir Buat Ujian Baru');
        $responseCreate->assertSee('Buat Ujian Baru');
        $responseCreate->assertSee('Guru Aktif');
    }
}

