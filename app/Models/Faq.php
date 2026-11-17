<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = ['pertanyaan', 'jawaban', 'kategori', 'urutan', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'urutan' => 'integer'];

    public const KATEGORI = [
        'umum' => 'Umum',
        'pendaftaran' => 'Pendaftaran & Antrian',
        'layanan' => 'Layanan',
        'biaya' => 'Biaya & Asuransi',
    ];

    public function scopeAktif($q)
    {
        return $q->where('is_active', true)->orderBy('urutan');
    }
}
