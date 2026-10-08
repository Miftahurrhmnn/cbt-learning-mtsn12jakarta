<?php

namespace Tests\Feature;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_and_views_correct_stats(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Administrator CBT',
            'email' => 'admin@mtsn12.sch.id',
        ]);

        $classroom = Classroom::create(['name' => 'Kelas VII-A']);
        $subject = Subject::create(['name' => 'Matematika']);

        $guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Pengajar',
        ]);

        $siswa = User::factory()->create([
            'role' => 'siswa',
            'name' => 'Siswa Pintar',
            'classroom_id' => $classroom->id,
            'nisn' => '1234567890',
        ]);

        $exam = Exam::create([
            'title' => 'Ujian Semester 1',
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'user_id' => $guru->id,
            'duration' => 60,
            'status' => 'published',
            'token' => 'UAS2026',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Portal Administrator');
        $response->assertSee('Selamat Datang, Administrator CBT');
        $response->assertSee('Total Siswa');
        $response->assertSee('Total Kelas');
        $response->assertSee('Total Guru');
        $response->assertSee('Paket Ujian');
        $response->assertSee('Siswa Pintar');
        $response->assertSee('Kelas VII-A');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        // Guru cannot access admin dashboard
        $this->actingAs($guru)->get(route('admin.dashboard'))
            ->assertRedirect(route('guru.ujian.index'));

        // Siswa cannot access admin dashboard
        $this->actingAs($siswa)->get(route('admin.dashboard'))
            ->assertRedirect(route('siswa.dashboard'));

        // Guest cannot access admin dashboard
        $this->post('/logout');
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }
}
