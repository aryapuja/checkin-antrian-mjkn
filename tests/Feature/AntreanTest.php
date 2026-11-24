<?php

namespace Tests\Feature;

use App\Models\Reservasi;
use Database\Seeders\ReservasiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntreanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ReservasiSeeder::class);
    }

    public function test_can_view_index_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Antrean Mobile JKN');
    }

    public function test_can_get_table_data_ajax(): void
    {
        $response = $this->getJson('/api/antrean-data?tab=today_mjkn');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'html',
            'pagination',
            'total',
            'metrics' => [
                'total_today',
                'mjkn_today',
                'pending_today',
                'checked_in_today',
                'cancelled_today',
            ],
        ]);
    }

    public function test_can_checkin_antrean(): void
    {
        $reservasi = Reservasi::where('STATUS', 1)->first();
        $this->assertNotNull($reservasi);

        $response = $this->postJson("/api/antrean/{$reservasi->ID}/checkin");
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $this->assertDatabaseHas('reservasi', [
            'ID' => $reservasi->ID,
            'STATUS' => 2,
        ]);
    }

    public function test_can_batal_antrean(): void
    {
        $reservasi = Reservasi::where('STATUS', 1)->first();
        $this->assertNotNull($reservasi);

        $response = $this->postJson("/api/antrean/{$reservasi->ID}/batal", [
            'keterangan' => 'Pasien membatalkan janji temu',
        ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $this->assertDatabaseHas('reservasi', [
            'ID' => $reservasi->ID,
            'STATUS' => 0,
        ]);
    }
}
