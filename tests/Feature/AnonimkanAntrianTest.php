<?php

namespace Tests\Feature;

use App\Models\Antrian;
use App\Models\Pengaturan;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnonimkanAntrianTest extends TestCase
{
    use RefreshDatabase;

    private function poli(): Poli
    {
        return Poli::create(['nama' => 'Poli Umum', 'slug' => 'poli-umum']);
    }

    private function antrian(int $umurHari, int $nomor = 1): Antrian
    {
        $tanggal = now()->subDays($umurHari);

        return Antrian::create([
            'tanggal' => $tanggal->toDateString(),
            'poli_id' => $this->poli()->id,
            'nomor' => $nomor,
            'kode' => 'U-'.str_pad((string) $nomor, 3, '0', STR_PAD_LEFT),
            'nama_pasien' => 'Budi Santoso',
            'nik' => '1471010101010001',
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
            'keluhan' => 'Demam tiga hari',
            'status' => 'selesai',
        ]);
    }

    public function test_antrian_lama_dianonimkan(): void
    {
        $lama = $this->antrian(120);

        $this->artisan('antrian:anonimkan')->assertSuccessful();

        $lama->refresh();
        $this->assertSame('Pasien', $lama->nama_pasien);
        $this->assertNull($lama->nik);
        $this->assertNull($lama->no_hp);
        $this->assertNull($lama->keluhan);
        $this->assertNotNull($lama->anonim_pada);
        // Status, nomor, dan tanggal tetap: statistik tidak rusak.
        $this->assertSame('selesai', $lama->status);
        $this->assertSame(1, $lama->nomor);
    }

    public function test_antrian_baru_tidak_disentuh(): void
    {
        $baru = $this->antrian(3);

        $this->artisan('antrian:anonimkan')->assertSuccessful();

        $baru->refresh();
        $this->assertSame('Budi Santoso', $baru->nama_pasien);
        $this->assertSame('1471010101010001', $baru->nik);
        $this->assertNull($baru->anonim_pada);
    }

    public function test_batas_bawaan_diambil_dari_pengaturan(): void
    {
        Pengaturan::set('retensi_hari', '30', 'privasi');
        $umur45 = $this->antrian(45, 1);

        $this->artisan('antrian:anonimkan')->assertSuccessful();

        $this->assertNotNull($umur45->refresh()->anonim_pada);
    }

    public function test_opsi_hari_menimpa_pengaturan(): void
    {
        Pengaturan::set('retensi_hari', '365', 'privasi');
        $umur45 = $this->antrian(45, 1);

        $this->artisan('antrian:anonimkan', ['--hari' => 30])->assertSuccessful();

        $this->assertNotNull($umur45->refresh()->anonim_pada);
    }

    public function test_dry_run_tidak_mengubah_data(): void
    {
        $lama = $this->antrian(120);

        $this->artisan('antrian:anonimkan', ['--dry-run' => true])
            ->expectsOutputToContain('akan dianonimkan')
            ->assertSuccessful();

        $lama->refresh();
        $this->assertSame('Budi Santoso', $lama->nama_pasien);
        $this->assertNull($lama->anonim_pada);
    }

    public function test_dijalankan_dua_kali_tidak_mengubah_apa_pun_lagi(): void
    {
        $this->antrian(120);

        $this->artisan('antrian:anonimkan')->assertSuccessful();
        $this->artisan('antrian:anonimkan')
            ->expectsOutputToContain('0 antrian dianonimkan')
            ->assertSuccessful();
    }
}
