<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';

    protected $fillable = [
        'judul', 'slug', 'kategori', 'ringkasan', 'isi', 'is_published', 'published_at', 'user_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public const KATEGORI = [
        'umum' => 'Umum',
        'jadwal' => 'Jadwal',
        'kesehatan' => 'Kesehatan',
        'layanan' => 'Layanan',
    ];

    protected static function booted(): void
    {
        static::saving(function (Pengumuman $p) {
            if (blank($p->slug)) {
                $p->slug = Str::slug($p->judul) . '-' . Str::lower(Str::random(4));
            }
            if ($p->is_published && blank($p->published_at)) {
                $p->published_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTerbit($q)
    {
        return $q->where('is_published', true)->whereNotNull('published_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function labelKategori(): string
    {
        return self::KATEGORI[$this->kategori] ?? ucfirst($this->kategori);
    }
}
