<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Layanan extends Model
{
    protected $fillable = [
        'poli_id', 'nama', 'slug', 'ringkasan', 'deskripsi', 'persyaratan',
        'durasi_menit', 'butuh_rujukan', 'is_active', 'urutan',
    ];

    protected $casts = [
        'persyaratan' => 'array',
        'butuh_rujukan' => 'boolean',
        'is_active' => 'boolean',
        'durasi_menit' => 'integer',
        'urutan' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (Layanan $l) {
            if (blank($l->slug)) {
                $l->slug = Str::slug($l->nama);
            }
        });
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
