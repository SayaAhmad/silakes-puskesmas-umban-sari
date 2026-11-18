<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Antrian extends Model
{
    public const STATUS = [
        'menunggu' => 'Menunggu',
        'dipanggil' => 'Dipanggil',
        'selesai' => 'Selesai',
        'lewat' => 'Terlewat',
        'batal' => 'Dibatalkan',
    ];

    protected $fillable = [
        'tanggal', 'poli_id', 'nomor', 'kode', 'nama_pasien', 'nik', 'no_hp',
        'jenis_kelamin', 'keluhan', 'status', 'sumber', 'dipanggil_at', 'selesai_at',
        'anonim_pada',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'dipanggil_at' => 'datetime',
        'selesai_at' => 'datetime',
        'anonim_pada' => 'datetime',
        'nomor' => 'integer',
    ];

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    /** Nomor urut berikutnya untuk poli pada tanggal tertentu. */
    public static function nomorBerikutnya(int $poliId, ?string $tanggal = null): int
    {
        $tanggal ??= now()->toDateString();

        return (int) static::where('poli_id', $poliId)
            ->whereDate('tanggal', $tanggal)
            ->max('nomor') + 1;
    }

    public static function buatKode(Poli $poli, int $nomor): string
    {
        return $poli->kodeHuruf() . '-' . str_pad((string) $nomor, 3, '0', STR_PAD_LEFT);
    }

    /** Estimasi jam dilayani: acuan nomor yang sedang dipanggil, dikali durasi layanan. */
    public function estimasi(): ?string
    {
        $jadwal = Jadwal::where('poli_id', $this->poli_id)
            ->where('hari', $this->tanggal->dayOfWeek)
            ->where('is_active', true)
            ->orderBy('jam_mulai')
            ->first();

        if (! $jadwal) {
            return null;
        }

        $layanan = Layanan::where('poli_id', $this->poli_id)->where('is_active', true)->first();
        $durasi = $layanan?->durasi_menit ?: 15;

        // Acuan: nomor yang sedang dipanggil bila ada, jika tidak jam mulai praktik.
        $dipanggil = static::where('poli_id', $this->poli_id)
            ->whereDate('tanggal', $this->tanggal)
            ->where('status', 'dipanggil')
            ->orderByDesc('nomor')
            ->first();

        if ($dipanggil) {
            $acuan = $dipanggil->dipanggil_at?->copy() ?? now();
            $selisih = max(0, $this->nomor - $dipanggil->nomor);
        } else {
            $acuan = \Carbon\Carbon::parse($this->tanggal->toDateString() . ' ' . $jadwal->jam_mulai);
            $selisih = max(0, $this->nomor - 1);
        }

        $estimasi = $acuan->addMinutes($selisih * $durasi);

        // Jangan tampilkan jam yang sudah lewat.
        if ($estimasi->isPast()) {
            $estimasi = now();
        }

        return $estimasi->format('H:i');
    }

    public function scopeHariIni($q)
    {
        return $q->whereDate('tanggal', now()->toDateString());
    }

    public function scopeAktif($q)
    {
        return $q->whereIn('status', ['menunggu', 'dipanggil']);
    }

    public static function generateKodeUnik(Poli $poli, int $nomor): string
    {
        $dasar = self::buatKode($poli, $nomor);
        $kode = $dasar;
        $i = 1;
        while (static::where('kode', $kode)->exists()) {
            $kode = $dasar . Str::upper(Str::random(2));
            if ($i++ > 20) {
                $kode = $dasar . '-' . Str::upper(Str::random(4));
                break;
            }
        }

        return $kode;
    }
}
