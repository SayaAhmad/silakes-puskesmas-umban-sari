<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jadwal extends Model
{
    public const HARI = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    protected $fillable = ['dokter_id', 'poli_id', 'hari', 'jam_mulai', 'jam_selesai', 'kuota', 'is_active'];

    protected $casts = [
        'hari' => 'integer',
        'kuota' => 'integer',
        'is_active' => 'boolean',
    ];

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function namaHari(): string
    {
        return self::HARI[$this->hari] ?? '-';
    }

    public function getJamAttribute(): string
    {
        return substr($this->jam_mulai, 0, 5) . '–' . substr($this->jam_selesai, 0, 5);
    }

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeHariIni($q)
    {
        return $q->where('hari', now()->dayOfWeek);
    }
}
