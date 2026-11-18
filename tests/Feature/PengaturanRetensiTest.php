<?php

namespace Tests\Feature;

use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanRetensiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_active' => true]);
    }

    private function isian(array $ubah = []): array
    {
        return array_merge([
            'nama_puskesmas' => 'Puskesmas Umban Sari',
            'jam_buka' => '08:00',
            'jam_tutup' => '14:00',
            'hari_kerja' => 'Senin–Jumat',
            'retensi_hari' => '90',
        ], $ubah);
    }

    public function test_admin_bisa_menyimpan_batas_retensi(): void
    {
        $this->actingAs($this->admin())
            ->put('/panel/pengaturan', $this->isian(['retensi_hari' => '60']))
            ->assertSessionHasNoErrors();

        $this->assertSame('60', Pengaturan::get('retensi_hari'));
    }

    public function test_batas_retensi_harus_angka_masuk_akal(): void
    {
        foreach (['0', '-5', 'abc', '99999'] as $nilai) {
            $this->actingAs($this->admin())
                ->put('/panel/pengaturan', $this->isian(['retensi_hari' => $nilai]))
                ->assertSessionHasErrors('retensi_hari');
        }
    }
}
