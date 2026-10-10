<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherMultiSubjectTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;
    private Classroom $classroom;
    private Subject $subjectMatematika;
    private Subject $subjectIPA;
    private Subject $subjectBahasa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Budi Pengajar',
            'email' => 'budi@sekolah.id',
        ]);

        $this->classroom = Classroom::create([
            'name' => 'Kelas VIII-A',
        ]);

        $this->subjectMatematika = Subject::create([
            'name' => 'Matematika',
        ]);

        $this->subjectIPA = Subject::create([
            'name' => 'Ilmu Pengetahuan Alam (IPA)',
        ]);

        $this->subjectBahasa = Subject::create([
            'name' => 'Bahasa Indonesia',
        ]);
    }

    /**
     * Test 1: Guru can hold multiple subjects via syncSubjects
     */
    public function test_guru_can_hold_multiple_subjects(): void
    {
        $this->guru->syncSubjects([
            $this->subjectMatematika->id,
            $this->subjectIPA->id,
        ]);

        $this->assertEquals(2, $this->guru->subjects()->count());
        $this->assertTrue($this->guru->teachesSubject($this->subjectMatematika->id));
        $this->assertTrue($this->guru->teachesSubject($this->subjectIPA->id));
        $this->assertFalse($this->guru->teachesSubject($this->subjectBahasa->id));
    }

    /**
     * Test 2: Guru can sync subjects via web route POST /guru/mata-pelajaran/sync
     */
    public function test_guru_can_sync_subjects_via_post_route(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.subjects.sync'), [
            'subject_ids' => [
                $this->subjectMatematika->id,
                $this->subjectBahasa->id,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(2, $this->guru->subjects()->count());
        $this->assertTrue($this->guru->fresh()->teachesSubject($this->subjectMatematika->id));
        $this->assertTrue($this->guru->fresh()->teachesSubject($this->subjectBahasa->id));
        $this->assertFalse($this->guru->fresh()->teachesSubject($this->subjectIPA->id));
    }

    /**
     * Test 3: Guru cannot sync empty subject list
     */
    public function test_sync_subjects_validates_presence_of_subjects(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.subjects.sync'), [
            'subject_ids' => [],
        ]);

        $response->assertSessionHasErrors('subject_ids');
    }

    /**
     * Test 4: Guru can create exams and input questions across multiple subjects
     */
    public function test_guru_can_create_exams_and_questions_in_multiple_subjects(): void
    {
        // Guru holds Matematika and IPA
        $this->guru->syncSubjects([
            $this->subjectMatematika->id,
            $this->subjectIPA->id,
        ]);

        // Create exam for Matematika
        $responseMat = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => 'Ujian Harian Matematika',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_ids' => [$this->classroom->id],
            'status' => 'draft',
            'start_time' => '08:00',
            'end_time' => '09:30',
        ]);
        $responseMat->assertRedirect();
        $matExam = Exam::where('title', 'Ujian Harian Matematika')->first();
        $this->assertNotNull($matExam);

        // Input question for Matematika exam
        $responseQ1 = $this->actingAs($this->guru)->post(route('guru.ujian.soal.store', $matExam->id), [
            'question_text' => 'Berapa 10 + 15?',
            'option_a' => '25',
            'option_b' => '20',
            'option_c' => '30',
            'option_d' => '15',
            'correct_answer' => 'A',
        ]);
        $responseQ1->assertRedirect(route('guru.ujian.show', $matExam->id));
        $this->assertEquals(1, $matExam->questions()->count());

        // Create exam for IPA
        $responseIPA = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => 'Ujian Praktik IPA',
            'subject_id' => $this->subjectIPA->id,
            'classroom_ids' => [$this->classroom->id],
            'status' => 'draft',
            'start_time' => '10:00',
            'end_time' => '11:30',
        ]);
        $responseIPA->assertRedirect();
        $ipaExam = Exam::where('title', 'Ujian Praktik IPA')->first();
        $this->assertNotNull($ipaExam);

        // Input question for IPA exam
        $responseQ2 = $this->actingAs($this->guru)->post(route('guru.ujian.soal.store', $ipaExam->id), [
            'question_text' => 'Fotosintesis menghasilkan zat apa?',
            'option_a' => 'Oksigen dan Glukosa',
            'option_b' => 'Karbon Dioksida',
            'option_c' => 'Nitrogen',
            'option_d' => 'Belerang',
            'correct_answer' => 'A',
        ]);
        $responseQ2->assertRedirect(route('guru.ujian.show', $ipaExam->id));
        $this->assertEquals(1, $ipaExam->questions()->count());
    }

    /**
     * Test 5: Guru selecting a subject they do not currently hold automatically assigns it
     */
    public function test_guru_automatically_assigned_subject_when_creating_exam(): void
    {
        // Guru initially has only Matematika
        $this->guru->syncSubjects([$this->subjectMatematika->id]);
        $this->assertFalse($this->guru->teachesSubject($this->subjectBahasa->id));

        // Guru chooses Bahasa Indonesia when creating an exam
        $response = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => 'Ujian Membaca Bahasa Indonesia',
            'subject_id' => $this->subjectBahasa->id,
            'classroom_ids' => [$this->classroom->id],
            'status' => 'draft',
            'start_time' => '07:30',
            'end_time' => '09:00',
        ]);
        $response->assertRedirect();

        // Guru now holds BOTH Matematika and Bahasa Indonesia
        $this->guru->refresh();
        $this->assertTrue($this->guru->teachesSubject($this->subjectMatematika->id));
        $this->assertTrue($this->guru->teachesSubject($this->subjectBahasa->id));
        $this->assertEquals(2, $this->guru->subjects()->count());
    }

    /**
     * Test 6: Bank soal renders teacher's subjects and subjects selection modal
     */
    public function test_bank_soal_page_renders_teacher_subjects(): void
    {
        $this->guru->syncSubjects([
            $this->subjectMatematika->id,
            $this->subjectIPA->id,
        ]);

        $response = $this->actingAs($this->guru)->get(route('guru.bank-soal.index'));

        $response->assertStatus(200);
        $response->assertSee('Bank Soal Guru');
        $response->assertSee('Matematika');
        $response->assertSee('Ilmu Pengetahuan Alam (IPA)');
    }

    /**
     * Test 7: Profile edit page displays teacher subjects form for guru
     */
    public function test_profile_edit_displays_teacher_subjects_section(): void
    {
        $this->guru->syncSubjects([$this->subjectMatematika->id]);

        $response = $this->actingAs($this->guru)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Mata Pelajaran yang Diampu');
        $response->assertSee('Matematika');
        $response->assertSee('Simpan Mata Pelajaran');
    }
}
