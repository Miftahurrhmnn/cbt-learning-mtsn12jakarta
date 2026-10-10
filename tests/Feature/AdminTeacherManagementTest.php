<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTeacherManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $guru;
    private User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->guru = User::factory()->create([
            'role' => 'guru',
        ]);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
        ]);
    }

    public function test_sidebar_contains_separate_data_guru_and_tambah_guru_menus(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.guru.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Guru');
        $response->assertSee('Tambah Guru');
        $response->assertSee(route('admin.guru.index'));
        $response->assertSee(route('admin.guru.create'));
    }

    public function test_non_admin_cannot_access_guru_management(): void
    {
        $this->actingAs($this->guru)
            ->get(route('admin.guru.index'))
            ->assertRedirect(route('guru.ujian.index'));

        $this->actingAs($this->guru)
            ->get(route('admin.guru.create'))
            ->assertRedirect(route('guru.ujian.index'));

        $this->actingAs($this->siswa)
            ->get(route('admin.guru.index'))
            ->assertRedirect(route('siswa.dashboard'));
    }

    public function test_admin_can_view_tambah_guru_page_and_see_subjects(): void
    {
        $subjectMat = Subject::create(['name' => 'Matematika']);
        $subjectBio = Subject::create(['name' => 'Biologi']);

        $response = $this->actingAs($this->admin)->get(route('admin.guru.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Guru Baru');
        $response->assertSee('Matematika');
        $response->assertSee('Biologi');
    }

    public function test_admin_can_store_new_guru_with_multiple_subjects(): void
    {
        $subject1 = Subject::create(['name' => 'Fisika']);
        $subject2 = Subject::create(['name' => 'Kimia']);

        $response = $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Budi Santoso, M.Pd.',
            'email' => 'budi.santoso@cbt.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'subject_ids' => [$subject1->id, $subject2->id],
        ]);

        $response->assertRedirect(route('admin.guru.index'));
        $response->assertSessionHas('success');

        $newTeacher = User::where('email', 'budi.santoso@cbt.local')->first();
        $this->assertNotNull($newTeacher);
        $this->assertSame('guru', $newTeacher->role);
        $this->assertSame('Budi Santoso, M.Pd.', $newTeacher->name);
        $this->assertTrue(Hash::check('password123', $newTeacher->password));

        $this->assertCount(2, $newTeacher->subjects);
        $this->assertTrue($newTeacher->subjects->pluck('id')->contains($subject1->id));
        $this->assertTrue($newTeacher->subjects->pluck('id')->contains($subject2->id));
    }

    public function test_admin_can_filter_and_search_teachers(): void
    {
        $subject = Subject::create(['name' => 'Bahasa Inggris']);
        $teacher = User::factory()->create([
            'name' => 'Jane Teacher',
            'email' => 'jane@example.com',
            'role' => 'guru',
        ]);
        $teacher->syncSubjects([$subject->id]);

        $otherTeacher = User::factory()->create([
            'name' => 'John Unknown',
            'email' => 'john@example.com',
            'role' => 'guru',
        ]);

        $searchResponse = $this->actingAs($this->admin)->get(route('admin.guru.index', ['q' => 'Jane']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Jane Teacher');
        $searchResponse->assertDontSee('John Unknown');

        $filterResponse = $this->actingAs($this->admin)->get(route('admin.guru.index', ['subject_id' => $subject->id]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Jane Teacher');
        $filterResponse->assertDontSee('John Unknown');
    }

    public function test_admin_can_update_teacher(): void
    {
        $subject1 = Subject::create(['name' => 'Ekonomi']);
        $subject2 = Subject::create(['name' => 'Geografi']);

        $teacher = User::factory()->create([
            'name' => 'Guru Lama',
            'email' => 'lama@example.com',
            'role' => 'guru',
        ]);
        $teacher->syncSubjects([$subject1->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.guru.update', $teacher), [
            'name' => 'Guru Terupdate',
            'email' => 'baru@example.com',
            'subject_ids' => [$subject2->id],
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $teacher->refresh();
        $this->assertSame('Guru Terupdate', $teacher->name);
        $this->assertSame('baru@example.com', $teacher->email);
        $this->assertTrue($teacher->subjects->pluck('id')->contains($subject2->id));
        $this->assertFalse($teacher->subjects->pluck('id')->contains($subject1->id));
    }

    public function test_admin_can_delete_teacher(): void
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.guru.destroy', $teacher));

        $response->assertRedirect(route('admin.guru.index'));
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
    }
}
