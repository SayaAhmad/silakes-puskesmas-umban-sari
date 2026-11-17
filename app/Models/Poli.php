<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Poli extends Model
{
    protected $table = 'polis';

    protected $fillable = ['nama', 'slug', 'ikon', 'warna', 'deskripsi', 'urutan', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'urutan' => 'integer'];

    protected static function booted(): void
    {
        static::saving(function (Poli $poli) {
            if (blank($poli->slug)) {
                $poli->slug = Str::slug($poli->nama);
            }
        });
    }

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class);
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function dokters(): HasMany
    {
        return $this->hasMany(Dokter::class);
    }

    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeUrut($q)
    {
        return $q->orderBy('urutan')->orderBy('nama');
    }

    /** Huruf kode antrian, mis. Poli Umum => U */
    public function kodeHuruf(): string
    {
        // Kode pendek agar antrian tiap poli tidak bertabrakan (Gigi vs Gizi).
        $kamus = [
            'umum' => 'U', 'gigi' => 'G', 'kia' => 'K',
            'anak' => 'A', 'gizi' => 'Z', 'tb paru' => 'T',
        ];

        $kunci = strtolower(trim(preg_replace('/^poli\s+/i', '', (string) $this->nama)));

        if (isset($kamus[$kunci])) {
            return $kamus[$kunci];
        }

        return strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $kunci) ?: 'A', 0, 1));
    }
}
