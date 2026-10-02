<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use App\Services\DocxQuestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DocxQuestionImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_download_docx_template(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get(route('guru.ujian.soal.template'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=template_soal_cbt_mtsn12.docx');
    }

    public function test_guru_can_import_questions_from_valid_docx(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::create(['name' => 'Ilmu Pengetahuan Alam']);
        $classroom = Classroom::create(['name' => 'Kelas VIII-A']);

        $exam = Exam::create([
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'title' => 'Ujian Akhir IPA',
            'duration' => 60,
            'status' => 'draft',
        ]);

        // Generate actual template docx file from service
        $docxService = app(DocxQuestionService::class);
        $tempDocx = $docxService->createTemplateFile();

        $uploadedFile = new UploadedFile(
            $tempDocx,
            'template_soal_cbt_mtsn12.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($guru)->post(route('guru.ujian.soal.import_docx', $exam->id), [
            'docx_file' => $uploadedFile,
        ]);

        $response->assertRedirect(route('guru.ujian.show', $exam->id));
        $response->assertSessionHas('success');

        // Check questions created in database
        $this->assertDatabaseCount('questions', 4);

        $firstQuestion = Question::where('exam_id', $exam->id)->first();
        $this->assertNotNull($firstQuestion);
        $this->assertStringContainsString('Ibu kota negara Indonesia', $firstQuestion->question_text);
        $this->assertEquals('DKI Jakarta', $firstQuestion->option_a);
        $this->assertEquals('A', $firstQuestion->correct_answer);

        $secondQuestion = Question::where('exam_id', $exam->id)->skip(1)->first();
        $this->assertStringContainsString('25 x 4', $secondQuestion->question_text);
        $this->assertEquals('C', $secondQuestion->correct_answer);
    }

    public function test_import_requires_valid_docx_file(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $subject = Subject::create(['name' => 'Matematika']);
        $classroom = Classroom::create(['name' => 'Kelas VII-A']);

        $exam = Exam::create([
            'user_id' => $guru->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'title' => 'Ujian Matematika',
            'duration' => 60,
            'status' => 'draft',
        ]);

        // Upload non-docx file
        $txtFile = UploadedFile::fake()->create('document.txt', 10, 'text/plain');

        $response = $this->actingAs($guru)->post(route('guru.ujian.soal.import_docx', $exam->id), [
            'docx_file' => $txtFile,
        ]);

        $response->assertSessionHasErrors('docx_file');
        $this->assertDatabaseCount('questions', 0);
    }
}
