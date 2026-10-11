<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherExamIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $guruA;
    private User $guruB;
    private Classroom $classroom;
    private Subject $subjectMatematika;

    protected function setUp(): void
    {
        parent::setUp();

        $this->classroom = Classroom::create(['name' => 'Kelas X-A']);
        $this->subjectMatematika = Subject::create(['name' => 'Matematika']);

        // Guru A: seftiharneli@teacher.mtsn12.sch.id
        $this->guruA = User::factory()->create([
            'name' => 'Sefti Harneli, M.Pd.',
            'email' => 'seftiharneli@teacher.mtsn12.sch.id',
            'role' => 'guru',
        ]);
        $this->guruA->subjects()->attach($this->subjectMatematika->id);

        // Guru B: guru lain yang mengampu mata pelajaran yang sama
        $this->guruB = User::factory()->create([
            'name' => 'Guru Lain, S.Pd.',
            'email' => 'gurulain@teacher.mtsn12.sch.id',
            'role' => 'guru',
        ]);
        $this->guruB->subjects()->attach($this->subjectMatematika->id);
    }

    public function test_guru_can_only_see_their_own_exams_in_index(): void
    {
        // Guru A membuat ujian
        $examA = Exam::create([
            'title' => 'Ujian Milik Guru Sefti',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guruA->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'SEFTI1',
        ]);

        // Guru B membuat ujian
        $examB = Exam::create([
            'title' => 'Ujian Milik Guru Lain',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guruB->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'LAIN01',
        ]);

        // Guru A hanya melihat ujian miliknya sendiri
        $responseA = $this->actingAs($this->guruA)->get(route('guru.ujian.index'));
        $responseA->assertStatus(200);
        $responseA->assertSee('Ujian Milik Guru Sefti');
        $responseA->assertDontSee('Ujian Milik Guru Lain');

        // Guru B hanya melihat ujian miliknya sendiri
        $responseB = $this->actingAs($this->guruB)->get(route('guru.ujian.index'));
        $responseB->assertStatus(200);
        $responseB->assertSee('Ujian Milik Guru Lain');
        $responseB->assertDontSee('Ujian Milik Guru Sefti');
    }

    public function test_other_guru_cannot_view_or_show_another_guru_exam(): void
    {
        $examA = Exam::create([
            'title' => 'Ujian Khusus Guru Sefti',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guruA->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'SEFTI2',
        ]);

        // Guru A bisa melihat detail ujian miliknya
        $responseA = $this->actingAs($this->guruA)->get(route('guru.ujian.show', $examA->id));
        $responseA->assertStatus(200);

        // Guru B dilarang (403 Forbidden) melihat detail ujian milik Guru A
        $responseB = $this->actingAs($this->guruB)->get(route('guru.ujian.show', $examA->id));
        $responseB->assertStatus(403);
    }

    public function test_other_guru_cannot_add_edit_or_delete_questions_on_another_guru_exam(): void
    {
        $examA = Exam::create([
            'title' => 'Ujian Aljabar Guru Sefti',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guruA->id,
            'duration' => 60,
            'status' => 'draft',
            'token' => 'SEFTI3',
        ]);

        $questionA = Question::create([
            'exam_id' => $examA->id,
            'subject_id' => $this->subjectMatematika->id,
            'question_text' => 'Berapa 5 + 5?',
            'option_a' => '10',
            'option_b' => '11',
            'option_c' => '12',
            'option_d' => '13',
            'correct_answer' => 'A',
        ]);

        // Guru B dilarang membuka form tambah soal ujian Guru A
        $this->actingAs($this->guruB)->get(route('guru.ujian.soal.create', $examA->id))
            ->assertStatus(403);

        // Guru B dilarang menyimpan soal ke ujian Guru A
        $this->actingAs($this->guruB)->post(route('guru.ujian.soal.store', $examA->id), [
            'question_text' => 'Soal dari penyusup',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_answer' => 'A',
        ])->assertStatus(403);

        // Guru B dilarang mengedit soal milik Guru A
        $this->actingAs($this->guruB)->get(route('guru.soal.edit', $questionA->id))
            ->assertStatus(403);

        // Guru B dilarang mengupdate soal milik Guru A
        $this->actingAs($this->guruB)->put(route('guru.soal.update', $questionA->id), [
            'question_text' => 'Diubah oleh guru lain',
            'option_a' => '10',
            'option_b' => '11',
            'option_c' => '12',
            'option_d' => '13',
            'correct_answer' => 'A',
        ])->assertStatus(403);

        // Guru B dilarang menghapus soal milik Guru A
        $this->actingAs($this->guruB)->delete(route('guru.soal.destroy', $questionA->id))
            ->assertStatus(403);

        // Guru B dilarang menghapus ujian milik Guru A
        $this->actingAs($this->guruB)->delete(route('guru.ujian.destroy', $examA->id))
            ->assertStatus(403);

        // Guru B dilarang mengubah status (toggle) ujian milik Guru A
        $this->actingAs($this->guruB)->post(route('guru.ujian.toggle_status', $examA->id))
            ->assertStatus(403);
    }

    public function test_bank_soal_only_shows_questions_and_exams_created_by_logged_in_guru(): void
    {
        $examA = Exam::create([
            'title' => 'Bank Soal Ujian Sefti',
            'subject_id' => $this->subjectMatematika->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guruA->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'BS001',
        ]);

        Question::create([
            'exam_id' => $examA->id,
            'subject_id' => $this->subjectMatematika->id,
            'question_text' => 'Soal Unik Guru Sefti Nomor 999',
            'option_a' => '1',
            'option_b' => '2',
            'option_c' => '3',
            'option_d' => '4',
            'correct_answer' => 'A',
        ]);

        // Guru B mengakses bank soal
        $responseB = $this->actingAs($this->guruB)->get(route('guru.bank-soal.index'));
        $responseB->assertStatus(200);
        $responseB->assertDontSee('Bank Soal Ujian Sefti');

        // Guru B mencoba mengakses pertanyaan pada ujian Guru A via exam_id
        $responseBQuestions = $this->actingAs($this->guruB)->get(route('guru.bank-soal.index', ['exam_id' => $examA->id]));
        $responseBQuestions->assertStatus(200);
        $responseBQuestions->assertDontSee('Soal Unik Guru Sefti Nomor 999');

        // Guru A mengakses bank soal miliknya (melihat kartu ujian)
        $responseA = $this->actingAs($this->guruA)->get(route('guru.bank-soal.index'));
        $responseA->assertStatus(200);
        $responseA->assertSee('Bank Soal Ujian Sefti');

        // Guru A membuka butir soal pada ujian miliknya
        $responseAQuestions = $this->actingAs($this->guruA)->get(route('guru.bank-soal.index', ['exam_id' => $examA->id]));
        $responseAQuestions->assertStatus(200);
        $responseAQuestions->assertSee('Soal Unik Guru Sefti Nomor 999');
    }
}
