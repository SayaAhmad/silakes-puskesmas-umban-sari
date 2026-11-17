<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    protected $table = 'dokters';

    protected $fillable = ['poli_id', 'nama', 'gelar', 'sip', 'bio', 'is_active', 'urutan'];

    protected $casts = ['is_active' => 'boolean', 'urutan' => 'integer'];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function namaLengkap(): string
    {
        return trim($this->nama . ($this->gelar ? ', ' . $this->gelar : ''));
    }

    public function inisial(): string
    {
        $bersih = preg_replace('/^(dr\.|drg\.|Ns\.|Bd\.)\s*/i', '', $this->nama);
        $kata = preg_split('/\s+/', trim($bersih));

        return strtoupper(substr($kata[0] ?? 'D', 0, 1) . substr($kata[1] ?? '', 0, 1));
    }

    public function scopeAktif($q)
    {
        return $q->where('is_active', true);
    }
}
