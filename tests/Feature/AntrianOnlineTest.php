<?php

namespace Tests\Feature;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Jadwal;
use App\Models\Pengaturan;
use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntrianOnlineTest extends TestCase
{
    use RefreshDatabase;

    private const NIK = '1471010101010001';

    private function poliDenganJadwal(string $nama = 'Poli Umum', string $slug = 'poli-umum'): Poli
    {
        $poli = Poli::create(['nama' => $nama, 'slug' => $slug]);
        $dokter = Dokter::create(['nama' => 'dr. Uji', 'poli_id' => $poli->id]);

        Jadwal::create([
            'dokter_id' => $dokter->id,
            'poli_id' => $poli->id,
            'hari' => now()->dayOfWeek,
            'jam_mulai' => '08:00',
            'jam_selesai' => '14:00',
            'is_active' => true,
        ]);

        return $poli;
    }

    private function isian(array $ubah = []): array
    {
        return array_merge([
            'nama_pasien' => 'Andi Saputra',
            'jenis_kelamin' => 'L',
        ], $ubah);
    }

    public function test_nik_sama_di_poli_yang_sama_ditolak(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(9));
        $poli = $this->poliDenganJadwal();

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id, 'nik' => self::NIK]))
            ->assertSessionHasNoErrors();

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id, 'nik' => self::NIK]))
            ->assertSessionHasErrors('nik');

        $this->assertDatabaseCount('antrians', 1);
    }

    public function test_nik_sama_di_poli_berbeda_diperbolehkan(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(9));
        $umum = $this->poliDenganJadwal('Poli Umum', 'poli-umum');
        $gigi = $this->poliDenganJadwal('Poli Gigi', 'poli-gigi');

        $this->post('/antrian', $this->isian(['poli_id' => $umum->id, 'nik' => self::NIK]))
            ->assertSessionHasNoErrors();

        $this->post('/antrian', $this->isian(['poli_id' => $gigi->id, 'nik' => self::NIK]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('antrians', 2);
    }

    public function test_nik_sama_boleh_lagi_setelah_antrian_sebelumnya_selesai(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(9));
        $poli = $this->poliDenganJadwal();

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id, 'nik' => self::NIK]));
        Antrian::first()->update(['status' => 'selesai']);

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id, 'nik' => self::NIK]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('antrians', 2);
    }

    public function test_tanpa_nik_boleh_mengambil_antrian_berulang(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(9));
        $poli = $this->poliDenganJadwal();

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id]))
            ->assertSessionHasNoErrors();

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('antrians', 2);
    }

    public function test_di_luar_jam_operasional_ditolak(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(20));
        Pengaturan::set('jam_buka', '08:00', 'operasional');
        Pengaturan::set('jam_tutup', '14:00', 'operasional');
        $poli = $this->poliDenganJadwal();

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id, 'nik' => self::NIK]))
            ->assertSessionHasErrors('poli_id');

        $this->assertDatabaseCount('antrians', 0);
    }

    public function test_di_dalam_jam_operasional_diterima(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(9));
        Pengaturan::set('jam_buka', '08:00', 'operasional');
        Pengaturan::set('jam_tutup', '14:00', 'operasional');
        $poli = $this->poliDenganJadwal();

        $this->post('/antrian', $this->isian(['poli_id' => $poli->id, 'nik' => self::NIK]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('antrians', 1);
    }
}
