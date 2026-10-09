<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruCalendarTest extends TestCase
{
    use RefreshDatabase;

    private User $guru;

    protected function setUp(): void
    {
        parent::setUp();

        $this->guru = User::factory()->create([
            'role' => 'guru',
            'name' => 'Guru Pengajar',
            'email' => 'guru@sekolah.id',
        ]);
    }

    /**
     * Test: Guru dapat mengakses halaman kalender
     */
    public function test_guru_can_access_calendar_page(): void
    {
        $response = $this->actingAs($this->guru)->get(route('guru.fullcalendar.index'));

        $response->assertStatus(200);
        $response->assertSee('Kalender Kegiatan dan Catatan');
        $response->assertSee('Panduan Warna Catatan:');
        $response->assertSee('Tambah Catatan Baru');
    }

    /**
     * Test: Guru dapat mengambil daftar kegiatan kalender via AJAX
     */
    public function test_guru_can_fetch_calendar_events_via_ajax(): void
    {
        Event::create([
            'user_id' => $this->guru->id,
            'title' => 'Rapat Dewan Guru',
            'description' => 'Membahas agenda PAT semester genap',
            'color' => '#7c3aed',
            'start' => Carbon::now()->toDateString(),
            'end' => Carbon::now()->addDay()->toDateString(),
        ]);

        $response = $this->actingAs($this->guru)->getJson(route('guru.fullcalendar.index', [
            'start' => Carbon::now()->subDays(7)->toDateString(),
            'end' => Carbon::now()->addDays(7)->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Rapat Dewan Guru',
            'description' => 'Membahas agenda PAT semester genap',
            'color' => '#7c3aed',
        ]);
    }

    /**
     * Test: Guru dapat menyimpan kegiatan baru lengkap dengan catatan dan warna
     */
    public function test_guru_can_add_event_with_notes_and_color(): void
    {
        $startDate = Carbon::now()->toDateString();
        $endDate = Carbon::now()->addDays(2)->toDateString();

        $response = $this->actingAs($this->guru)->postJson(route('guru.fullcalendar.ajax'), [
            'type' => 'add',
            'title' => 'Penilaian Harian Matematika',
            'description' => 'Materi Bab 3: Matriks dan Transformasi Geometri',
            'color' => '#2563eb',
            'start' => $startDate,
            'end' => $endDate,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Penilaian Harian Matematika',
            'description' => 'Materi Bab 3: Matriks dan Transformasi Geometri',
            'color' => '#2563eb',
            'success' => true,
        ]);

        $this->assertDatabaseHas('events_table_teacher', [
            'user_id' => $this->guru->id,
            'title' => 'Penilaian Harian Matematika',
            'description' => 'Materi Bab 3: Matriks dan Transformasi Geometri',
            'color' => '#2563eb',
            'start' => $startDate,
            'end' => $endDate,
        ]);
    }

    /**
     * Test: Guru dapat memperbarui judul, catatan, dan warna kegiatan
     */
    public function test_guru_can_update_event_notes_and_color(): void
    {
        $event = Event::create([
            'user_id' => $this->guru->id,
            'title' => 'Rapat Awal',
            'description' => 'Catatan sebelum revisi',
            'color' => '#4f46e5',
            'start' => Carbon::now()->toDateString(),
            'end' => Carbon::now()->toDateString(),
        ]);

        $response = $this->actingAs($this->guru)->postJson(route('guru.fullcalendar.ajax'), [
            'type' => 'update',
            'id' => $event->id,
            'title' => 'Rapat Revisi Kurikulum',
            'description' => 'Catatan yang sudah disempurnakan bersama kepala madrasah',
            'color' => '#dc2626',
            'start' => Carbon::now()->addDay()->toDateString(),
            'end' => Carbon::now()->addDay()->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'title' => 'Rapat Revisi Kurikulum',
            'description' => 'Catatan yang sudah disempurnakan bersama kepala madrasah',
            'color' => '#dc2626',
            'success' => true,
        ]);

        $this->assertDatabaseHas('events_table_teacher', [
            'id' => $event->id,
            'title' => 'Rapat Revisi Kurikulum',
            'description' => 'Catatan yang sudah disempurnakan bersama kepala madrasah',
            'color' => '#dc2626',
        ]);
    }

    /**
     * Test: Guru dapat menghapus kegiatan
     */
    public function test_guru_can_delete_event(): void
    {
        $event = Event::create([
            'user_id' => $this->guru->id,
            'title' => 'Kegiatan Dihapus',
            'description' => 'Akan dihapus',
            'color' => '#4f46e5',
            'start' => Carbon::now()->toDateString(),
            'end' => Carbon::now()->toDateString(),
        ]);

        $response = $this->actingAs($this->guru)->postJson(route('guru.fullcalendar.ajax'), [
            'type' => 'delete',
            'id' => $event->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('events_table_teacher', [
            'id' => $event->id,
        ]);
    }
}
