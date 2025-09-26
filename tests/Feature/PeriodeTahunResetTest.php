<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PeriodeTahun;
use App\Models\PeriodeTahap;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PeriodeTahunResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create test tahap
        PeriodeTahap::create(['tahap' => 'Rencana']);
        PeriodeTahap::create(['tahap' => 'Triwulan 1']);
        PeriodeTahap::create(['tahap' => 'Triwulan 2']);
        PeriodeTahap::create(['tahap' => 'Triwulan 3']);
        PeriodeTahap::create(['tahap' => 'Triwulan 4']);
    }

    /** @test */
    public function test_tahun_baru_otomatis_set_sebagai_aktif()
    {
        // Arrange: Buat tahun 2025 sebagai tahun aktif
        $tahun2025 = PeriodeTahun::create(['tahun' => 2025, 'status' => 1]);
        
        // Act: Buat tahun baru
        $response = $this->post('/monitoring/periode/lanjutkanKeTahunBerikutnya');
        
        // Assert: Tahun 2026 harus menjadi aktif, tahun 2025 tidak aktif
        $tahun2025->refresh();
        $tahun2026 = PeriodeTahun::where('tahun', 2026)->first();
        
        $this->assertEquals(0, $tahun2025->status, 'Tahun 2025 harus menjadi tidak aktif');
        $this->assertNotNull($tahun2026, 'Tahun 2026 harus dibuat');
        $this->assertEquals(1, $tahun2026->status, 'Tahun 2026 harus menjadi aktif');
    }

    /** @test */
    public function test_getTahunAktif_method_works_correctly()
    {
        // Arrange: Buat beberapa tahun
        PeriodeTahun::create(['tahun' => 2023, 'status' => 0]);
        PeriodeTahun::create(['tahun' => 2024, 'status' => 0]);
        $tahunAktif = PeriodeTahun::create(['tahun' => 2025, 'status' => 1]);
        
        // Act: Ambil tahun aktif
        $result = PeriodeTahun::getTahunAktif();
        
        // Assert: Harus mengembalikan tahun 2025
        $this->assertEquals($tahunAktif->id, $result->id);
        $this->assertEquals(2025, $result->tahun);
        $this->assertEquals(1, $result->status);
    }

    /** @test */
    public function test_getTahunAktif_auto_set_latest_if_no_active()
    {
        // Arrange: Buat beberapa tahun tanpa ada yang aktif
        PeriodeTahun::create(['tahun' => 2023, 'status' => 0]);
        PeriodeTahun::create(['tahun' => 2024, 'status' => 0]);
        PeriodeTahun::create(['tahun' => 2025, 'status' => 0]);
        
        // Act: Ambil tahun aktif
        $result = PeriodeTahun::getTahunAktif();
        
        // Assert: Harus otomatis set tahun 2025 sebagai aktif
        $this->assertEquals(2025, $result->tahun);
        $this->assertEquals(1, $result->status);
        
        // Verify di database
        $tahun2025 = PeriodeTahun::where('tahun', 2025)->first();
        $this->assertEquals(1, $tahun2025->status);
    }

    /** @test */
    public function test_setAsActive_method_works()
    {
        // Arrange: Buat beberapa tahun
        $tahun2023 = PeriodeTahun::create(['tahun' => 2023, 'status' => 1]);
        $tahun2024 = PeriodeTahun::create(['tahun' => 2024, 'status' => 0]);
        $tahun2025 = PeriodeTahun::create(['tahun' => 2025, 'status' => 0]);
        
        // Act: Set tahun 2025 sebagai aktif
        $tahun2025->setAsActive();
        
        // Assert: Hanya tahun 2025 yang aktif
        $tahun2023->refresh();
        $tahun2024->refresh();
        $tahun2025->refresh();
        
        $this->assertEquals(0, $tahun2023->status);
        $this->assertEquals(0, $tahun2024->status);
        $this->assertEquals(1, $tahun2025->status);
    }

    /** @test */
    public function test_periode_created_for_new_year()
    {
        // Arrange: Buat tahun 2025
        PeriodeTahun::create(['tahun' => 2025, 'status' => 1]);
        
        // Act: Buat tahun baru
        $response = $this->post('/monitoring/periode/lanjutkanKeTahunBerikutnya');
        
        // Assert: Periode harus dibuat untuk tahun 2026
        $tahun2026 = PeriodeTahun::where('tahun', 2026)->first();
        $this->assertNotNull($tahun2026);
        
        $periodeCount = Periode::where('tahun_id', $tahun2026->id)->count();
        $this->assertEquals(5, $periodeCount, 'Harus ada 5 periode (Rencana + 4 Triwulan)');
    }

    /** @test */
    public function test_duplicate_year_prevention()
    {
        // Arrange: Buat tahun 2025 dan 2026
        PeriodeTahun::create(['tahun' => 2025, 'status' => 1]);
        PeriodeTahun::create(['tahun' => 2026, 'status' => 0]);
        
        // Act: Coba buat tahun baru lagi
        $response = $this->post('/monitoring/periode/lanjutkanKeTahunBerikutnya');
        
        // Assert: Harus error karena tahun 2026 sudah ada
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Tahun 2026 sudah ada.']);
    }

    /** @test */
    public function test_transaction_rollback_on_error()
    {
        // Arrange: Buat tahun 2025
        PeriodeTahun::create(['tahun' => 2025, 'status' => 1]);
        
        // Mock error saat membuat periode
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('rollback')->once();
        
        // Act & Assert: Error handling harus bekerja
        $this->expectException(\Exception::class);
        
        // Simulate error by deleting tahap
        PeriodeTahap::truncate();
        
        $response = $this->post('/monitoring/periode/lanjutkanKeTahunBerikutnya');
    }
}
