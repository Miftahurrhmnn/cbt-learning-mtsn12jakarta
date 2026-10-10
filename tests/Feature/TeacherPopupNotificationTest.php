<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherPopupNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $guru;
    protected Subject $subject;
    protected Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = Subject::create(['name' => 'Fisika']);
        $this->classroom = Classroom::create(['name' => 'Kelas IX-A']);

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Fisika Hebat',
            'email' => 'guru.fisika@mtsn12.sch.id',
        ]);
        $this->guru->subjects()->attach($this->subject->id);
    }

    /**
     * Test 1: Halaman portal guru memuat library Toastr & styling pojok kanan atas
     */
    public function test_teacher_pages_render_toastr_and_top_right_popup_system(): void
    {
        $response = $this->actingAs($this->guru)->get(route('guru.ujian.index'));

        $response->assertStatus(200);
        $response->assertSee('toastr.min.css');
        $response->assertSee('toastr.min.js');
        $response->assertSee('toast-top-right');
        $response->assertSee('#toast-container');
    }

    /**
     * Test 2: Pembuatan ujian memunculkan popup toastr success di pojok kanan atas
     */
    public function test_exam_creation_redirects_and_triggers_toastr_success_popup(): void
    {
        $response = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => 'Ujian Akhir Semester Fisika',
            'subject_id' => $this->subject->id,
            'classroom_ids' => [$this->classroom->id],
            'duration' => 60,
            'status' => 'draft',
            'start_time' => '08:00',
            'end_time' => '09:00',
            'day_of_week' => 'Senin',
            'token' => 'FSK2026',
        ]);

        $exam = Exam::where('title', 'Ujian Akhir Semester Fisika')->first();
        $this->assertNotNull($exam);

        // Assert redirect to guru.ujian.show with flash success
        $response->assertRedirect(route('guru.ujian.show', $exam->id));
        $response->assertSessionHas('success');

        // Mengikuti redirect dan memastikan trigger script toastr.success ter-render
        $followResponse = $this->actingAs($this->guru)->get(route('guru.ujian.show', $exam->id));
        $followResponse->assertStatus(200);
        $followResponse->assertSee('toastr.success');
        $followResponse->assertSee('Ujian berhasil dibuat dengan Token [ FSK2026 ]');
    }

    /**
     * Test 3: Penghapusan ujian memunculkan popup toastr success di pojok kanan atas
     */
    public function test_exam_deletion_redirects_and_triggers_toastr_success_popup(): void
    {
        $exam = Exam::create([
            'title' => 'Ujian Yang Akan Dihapus',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'duration' => 45,
            'status' => 'draft',
            'token' => 'HAPUS1',
        ]);

        $response = $this->actingAs($this->guru)->delete(route('guru.ujian.destroy', $exam->id));

        $response->assertRedirect(route('guru.ujian.index'));
        $response->assertSessionHas('success', 'Ujian berhasil dihapus.');

        // Mengikuti redirect dan memastikan script toastr.success ter-render di halaman daftar ujian
        $followResponse = $this->actingAs($this->guru)->get(route('guru.ujian.index'));
        $followResponse->assertStatus(200);
        $followResponse->assertSee('toastr.success');
        $followResponse->assertSee('Ujian berhasil dihapus.');
    }

    /**
     * Test 4: Toggle status ujian memunculkan popup toastr success
     */
    public function test_exam_status_toggle_triggers_toastr_popup(): void
    {
        $exam = Exam::create([
            'title' => 'Ujian Status Toggle',
            'subject_id' => $this->subject->id,
            'classroom_id' => $this->classroom->id,
            'user_id' => $this->guru->id,
            'duration' => 45,
            'status' => 'draft',
            'token' => 'STAT01',
        ]);

        Question::create([
            'exam_id' => $exam->id,
            'subject_id' => $this->subject->id,
            'question_text' => 'Apa rumus kecepatan?',
            'option_a' => 'v = s/t',
            'option_b' => 'v = s*t',
            'option_c' => 'v = a*t',
            'option_d' => 'v = m*a',
            'correct_answer' => 'A',
        ]);

        $response = $this->actingAs($this->guru)->post(route('guru.ujian.toggle_status', $exam->id));

        $response->assertSessionHas('success');

        // Pastikan status berubah menjadi published
        $this->assertEquals('published', $exam->fresh()->status);
    }

    /**
     * Test 5: Error validasi form memunculkan popup toastr error
     */
    public function test_validation_error_triggers_toastr_error_popup(): void
    {
        // Kirim form tanpa mengisi judul
        $response = $this->actingAs($this->guru)->post(route('guru.ujian.store'), [
            'title' => '',
            'subject_id' => $this->subject->id,
            'classroom_ids' => [$this->classroom->id],
        ]);

        $response->assertSessionHasErrors(['title']);
    }
}
