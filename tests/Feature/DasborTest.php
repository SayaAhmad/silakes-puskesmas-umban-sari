<?php

namespace Tests\Feature;

use App\Models\Antrian;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DasborTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'petugas', bool $aktif = true): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => $aktif,
            'password' => 'rahasia123',
        ]);
    }

    private function poli(): Poli
    {
        return Poli::create([
            'nama' => 'Poli Umum',
            'slug' => 'poli-umum',
        ]);
    }

    public function test_tamu_diarahkan_ke_halaman_masuk(): void
    {
        $this->get('/panel')->assertRedirect('/masuk');
        $this->assertGuest();
    }

    public function test_akun_nonaktif_ditolak_walau_kata_sandi_benar(): void
    {
        $user = $this->user('petugas', aktif: false);

        $this->post('/masuk', [
            'email' => $user->email,
            'password' => 'rahasia123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_petugas_dan_admin_bisa_membuka_dasbor(): void
    {
        foreach (['petugas', 'admin'] as $role) {
            $this->actingAs($this->user($role))
                ->get('/panel')
                ->assertOk()
                ->assertSee('Selamat');
        }
    }

    public function test_rata_rata_waktu_tunggu_dihitung_dari_dibuat_ke_dipanggil(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(8));

        $poli = $this->poli();

        // Waktu tunggu 10, 20, 30 menit; durasi pelayanan seragam 60 menit.
        foreach ([10, 20, 30] as $i => $menit) {
            $dibuat = now()->addMinutes($i);

            $antrian = new Antrian([
                'tanggal' => now()->toDateString(),
                'poli_id' => $poli->id,
                'nomor' => $i + 1,
                'kode' => 'U-00'.($i + 1),
                'nama_pasien' => 'Pasien '.($i + 1),
                'status' => 'selesai',
                'dipanggil_at' => $dibuat->copy()->addMinutes($menit),
                'selesai_at' => $dibuat->copy()->addMinutes($menit + 60),
            ]);
            $antrian->created_at = $dibuat;
            $antrian->updated_at = $dibuat;
            $antrian->save();
        }

        // Rata-rata tunggu = (10 + 20 + 30) / 3 = 20 menit.
        // Kalau yang terhitung durasi pelayanan, angkanya akan 80.
        $this->actingAs($this->user('petugas'))
            ->get('/panel')
            ->assertOk()
            ->assertSee('Rata-rata tunggu 20 menit', false);
    }

    public function test_antrian_lebih_dari_30_hari_diabaikan(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(8));

        $poli = $this->poli();
        $lama = now()->subDays(45);

        $antrian = new Antrian([
            'tanggal' => $lama->toDateString(),
            'poli_id' => $poli->id,
            'nomor' => 1,
            'kode' => 'U-001',
            'nama_pasien' => 'Pasien Lama',
            'status' => 'selesai',
            'dipanggil_at' => $lama->copy()->addMinutes(90),
        ]);
        $antrian->created_at = $lama;
        $antrian->updated_at = $lama;
        $antrian->save();

        $this->actingAs($this->user('admin'))
            ->get('/panel')
            ->assertOk()
            ->assertDontSee('Rata-rata tunggu', false);
    }
}
