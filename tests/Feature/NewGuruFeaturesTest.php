<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewGuruFeaturesTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private Classroom $classA;
    private Classroom $classB;
    private Subject $subjectMatematika;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classA = Classroom::create(['name' => 'Kelas VII-A']);
        $this->classB = Classroom::create(['name' => 'Kelas VII-B']);
        $this->subjectMatematika = Subject::create(['name' => 'Matematika']);

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Miftah',
            'email' => 'miftah@mtsn12.sch.id',
        ]);
        $this->guru->subjects()->attach($this->subjectMatematika->id);
    }

    /**
     * Test 1: Bank Soal menu and page renders correctly
     */
    public function test_guru_can_access_bank_soal_page(): void
    {
        $exam = Exam::create([
            'title' => 'Ulangan Harian MTK 1',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classA->id,
            'user_id' => $this->guru->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'MTK0001',
        ]);

        $q = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $this->subjectMatematika->id,
            'question_text' => 'Berapakah 2 + 2?',
            'option_a' => '3',
            'option_b' => '4',
            'option_c' => '5',
            'option_d' => '6',
            'correct_answer' => 'B',
        ]);

        // 1. Tanpa memilih mapel: Tampilkan kartu nama mata pelajaran, butir soal belum dimunculkan
        $response = $this->actingAs($this->guru)->get(route('guru.bank-soal.index'));

        $response->assertStatus(200);
        $response->assertSee('Bank Soal Guru');
        $response->assertSee('Matematika');
        $response->assertSee('1 Soal');
        $response->assertSee('Buka Soal & Jawaban', false);
        $response->assertDontSee('Berapakah 2 + 2?');

        // 2. Setelah kartu mata pelajaran diklik: Tampilkan butir soal dan kunci jawabannya
        $responseSubject = $this->actingAs($this->guru)->get(route('guru.bank-soal.index', ['subject_id' => $this->subjectMatematika->id]));

        $responseSubject->assertStatus(200);
        $responseSubject->assertSee('Bank Soal: Matematika');
        $responseSubject->assertSee('Berapakah 2 + 2?');
        $responseSubject->assertSee('✓ Kunci Jawaban');
    }

    /**
     * Test 2: Guru can edit and update existing question
     */
    public function test_guru_can_edit_and_update_question(): void
    {
        Storage::fake('public');

        $exam = Exam::create([
            'title' => 'Ujian Aljabar',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classA->id,
            'user_id' => $this->guru->id,
            'duration' => 60,
            'status' => 'draft',
            'token' => 'ALJ1234',
        ]);

        $question = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $this->subjectMatematika->id,
            'question_text' => 'Berapakah nilai 5x jika x = 2?',
            'option_a' => '5',
            'option_b' => '7',
            'option_c' => '10',
            'option_d' => '12',
            'correct_answer' => 'C',
        ]);

        // Access edit form
        $responseEdit = $this->actingAs($this->guru)->get(route('guru.soal.edit', $question->id));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('Perbarui Butir Soal');
        $responseEdit->assertSee('Berapakah nilai 5x jika x = 2?');

        // Submit update
        $fakeImage = UploadedFile::fake()->image('diagram.png');
        $responseUpdate = $this->actingAs($this->guru)->put(route('guru.soal.update', $question->id), [
            'question_text' => 'Berapakah nilai 6x jika x = 3? (Diperbarui)',
            'image' => $fakeImage,
            'option_a' => '9',
            'option_b' => '12',
            'option_c' => '18',
            'option_d' => '24',
            'correct_answer' => 'C',
        ]);

        $responseUpdate->assertRedirect(route('guru.ujian.show', $exam->id));
        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'question_text' => 'Berapakah nilai 6x jika x = 3? (Diperbarui)',
            'option_c' => '18',
            'correct_answer' => 'C',
        ]);

        $updatedQuestion = Question::find($question->id);
        $this->assertNotNull($updatedQuestion->image);
        Storage::disk('public')->assertExists($updatedQuestion->image);
    }

    /**
     * Test 3: Guru creates exam with start_time, end_time, and multi-classrooms
     */
    public function test_guru_can_create_exam_with_schedule_time_and_multi_classrooms(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => 'Ujian Tengah Semester MTK Serentak',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_ids' => [$this->classA->id, $this->classB->id],
            'duration' => 90,
            'status' => 'published',
            'start_time' => '07:30',
            'end_time' => '09:30',
            'day_of_week' => 'Senin',
            'token' => 'UTS2026',
        ]);

        $exam = Exam::where('title', 'Ujian Tengah Semester MTK Serentak')->first();
        $this->assertNotNull($exam);
        $this->assertEquals('07:30', $exam->start_time);
        $this->assertEquals('09:30', $exam->end_time);

        // Verify both classrooms are attached in pivot
        $this->assertTrue($exam->classrooms->contains($this->classA->id));
        $this->assertTrue($exam->classrooms->contains($this->classB->id));

        // Test students from both classes are allowed
        $siswaA = User::factory()->create(['role' => 'siswa', 'classroom_id' => $this->classA->id]);
        $siswaB = User::factory()->create(['role' => 'siswa', 'classroom_id' => $this->classB->id]);
        $classOther = Classroom::create(['name' => 'Kelas VIII-C']);
        $siswaOther = User::factory()->create(['role' => 'siswa', 'classroom_id' => $classOther->id]);

        $this->assertTrue($exam->allowsClassroom($siswaA->classroom_id));
        $this->assertTrue($exam->allowsClassroom($siswaB->classroom_id));
        $this->assertFalse($exam->allowsClassroom($siswaOther->classroom_id));
    }

    /**
     * Test 4: Monitoring page displays ongoing, completed, and scores correctly
     */
    public function test_guru_can_monitor_students_and_view_matrix_box_details(): void
    {
        $exam = Exam::create([
            'title' => 'Monitoring Test Exam',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classA->id,
            'user_id' => $this->guru->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'MON2026',
        ]);

        $q1 = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $this->subjectMatematika->id,
            'question_text' => 'Soal Nomor 1',
            'option_a' => 'Benar 1',
            'option_b' => 'Salah 1',
            'option_c' => 'Salah 2',
            'option_d' => 'Salah 3',
            'correct_answer' => 'A',
        ]);

        $q2 = Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $this->subjectMatematika->id,
            'question_text' => 'Soal Nomor 2',
            'option_a' => 'Salah A',
            'option_b' => 'Benar B',
            'option_c' => 'Salah C',
            'option_d' => 'Salah D',
            'correct_answer' => 'B',
        ]);

        $siswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siti Aminah',
            'nisn' => '1234567890',
            'classroom_id' => $this->classA->id,
        ]);

        // Completed session with 1 correct and 1 wrong
        $session = ExamSession::create([
            'user_id' => $siswa->id,
            'exam_id' => $exam->id,
            'start_time' => now()->subMinutes(30),
            'end_time' => now(),
            'status' => 'completed',
            'score' => 50.00,
            'total_questions' => 2,
            'correct_answers' => 1,
        ]);

        ExamAnswer::create([
            'exam_session_id' => $session->id,
            'question_id' => $q1->id,
            'selected_answer' => 'A',
            'is_correct' => true,
        ]);

        ExamAnswer::create([
            'exam_session_id' => $session->id,
            'question_id' => $q2->id,
            'selected_answer' => 'C', // Salah, harusnya B
            'is_correct' => false,
        ]);

        // 1. Check Monitoring Index
        $responseIndex = $this->actingAs($this->guru)->get(route('guru.monitoring.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('Monitoring Pengerjaan Ujian Siswa');
        $responseIndex->assertSee('Siti Aminah');
        $responseIndex->assertSee('50.0');
        $responseIndex->assertSee('Selesai');

        // 2. Check Monitoring Detail (Matrix Kotak & Analisis)
        $responseDetail = $this->actingAs($this->guru)->get(route('guru.monitoring.detail', $session->id));
        $responseDetail->assertStatus(200);
        $responseDetail->assertSee('Analisis Jawaban Siswa');
        $responseDetail->assertSee('Matriks Kotak Hasil Jawaban Soal');
        $responseDetail->assertSee('Siti Aminah');
        $responseDetail->assertSee('Soal Nomor 1');
        $responseDetail->assertSee('Soal Nomor 2');
        $responseDetail->assertSee('Benar ✓');
        $responseDetail->assertSee('Salah ✕');
        $responseDetail->assertSee('Dipilih Siswa');
    }

    /**
     * Test 5: Guru can create exam without duration and views display status jam & classroom explanation
     */
    public function test_guru_can_create_exam_without_duration_and_displays_status_jam_and_classroom_explanation(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => 'Penilaian Harian Jam Pelaksanaan',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_ids' => [$this->classA->id, $this->classB->id],
            'status' => 'published',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'day_of_week' => 'Senin',
            'token' => 'JAM2026',
            // Notice: duration is omitted entirely!
        ]);

        $exam = Exam::where('title', 'Penilaian Harian Jam Pelaksanaan')->first();
        $this->assertNotNull($exam);
        $this->assertEquals(120, $exam->duration); // 2 hours = 120 minutes
        $this->assertEquals('08:00 - 10:00 WIB', $exam->formatted_time_range);
        $this->assertTrue(str_contains($exam->all_classroom_names, 'Kelas VII-A'));
        $this->assertTrue(str_contains($exam->all_classroom_names, 'Kelas VII-B'));

        // Check Guru Index page
        $responseIndex = $this->actingAs($this->guru)->get(route('guru.ujian.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee('08:00 - 10:00 WIB');
        $responseIndex->assertSee('Kelas Sasaran (Akses)');

        // Check Guru Show page has the classroom access explanation
        $responseShow = $this->actingAs($this->guru)->get(route('guru.ujian.show', $exam->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Penjelasan Hak Akses Kelas Siswa:');
        $responseShow->assertSee('Status Jam Pelaksanaan');
        $responseShow->assertSee('08:00 - 10:00 WIB');
    }

    /**
     * Test 6: Siswa dashboard and token pages display status jam and classroom explanation
     */
    public function test_siswa_dashboard_and_token_pages_display_status_jam_and_classroom_explanation(): void
    {
        $exam = Exam::create([
            'title' => 'Ujian Siswa Jam Khusus',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classA->id,
            'user_id' => $this->guru->id,
            'start_time' => '07:30',
            'end_time' => '09:00',
            'status' => 'published',
            'token' => 'JAM7390',
        ]);
        $exam->classrooms()->sync([$this->classA->id]);

        Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $this->subjectMatematika->id,
            'question_text' => 'Soal Ujian Jam',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_answer' => 'A',
        ]);

        $siswa = User::factory()->create([
            'role' => 'siswa',
            'classroom_id' => $this->classA->id,
        ]);

        // Dashboard check
        $responseDash = $this->actingAs($siswa)->get(route('siswa.dashboard'));
        $responseDash->assertStatus(200);
        $responseDash->assertSee('07:30 - 09:00 WIB');
        $responseDash->assertSee('Kelas VII-A');

        // Token page check
        $responseToken = $this->actingAs($siswa)->get(route('siswa.ujian.token', $exam->id));
        $responseToken->assertStatus(200);
        $responseToken->assertSee('07:30 - 09:00 WIB');
        $responseToken->assertSee('Akses Ujian Khusus:');
        $responseToken->assertSee('Kelas VII-A');
    }
}
