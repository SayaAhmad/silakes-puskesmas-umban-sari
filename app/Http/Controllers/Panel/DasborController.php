<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Jadwal;
use App\Models\Layanan;
use App\Models\Pengumuman;
use App\Models\Poli;
use Illuminate\Support\Facades\DB;

class DasborController extends Controller
{
    public function index()
    {
        $hariIni = now()->toDateString();

        $ringkasan = [
            'antrian_hari_ini' => Antrian::whereDate('tanggal', $hariIni)->count(),
            'menunggu' => Antrian::whereDate('tanggal', $hariIni)->where('status', 'menunggu')->count(),
            'dipanggil' => Antrian::whereDate('tanggal', $hariIni)->where('status', 'dipanggil')->count(),
            'selesai' => Antrian::whereDate('tanggal', $hariIni)->where('status', 'selesai')->count(),
        ];

        // Tren 7 hari terakhir
        $tren = Antrian::selectRaw('tanggal, count(*) as jumlah')
            ->whereBetween('tanggal', [now()->subDays(6)->toDateString(), $hariIni])
            ->groupBy('tanggal')
            ->pluck('jumlah', 'tanggal');

        $hariTren = collect(range(6, 0))->map(function ($mundur) use ($tren) {
            $tgl = now()->subDays($mundur);
            $kunci = $tgl->toDateString();

            return [
                'label' => $tgl->translatedFormat('D'),
                'tanggal' => $tgl->format('d/m'),
                'jumlah' => (int) ($tren[$kunci] ?? 0),
            ];
        });

        $perPoli = Poli::aktif()->urut()->get()->map(fn ($p) => [
            'nama' => $p->nama,
            'warna' => $p->warna,
            'jumlah' => Antrian::whereDate('tanggal', $hariIni)->where('poli_id', $p->id)->count(),
            'menunggu' => Antrian::whereDate('tanggal', $hariIni)->where('poli_id', $p->id)->where('status', 'menunggu')->count(),
        ]);

        return view('panel.dasbor', [
            'ringkasan' => $ringkasan,
            'hariTren' => $hariTren,
            'perPoli' => $perPoli,
            'antrianAktif' => Antrian::with('poli')->hariIni()->aktif()->orderBy('nomor')->limit(8)->get(),
            'jadwalHariIni' => Jadwal::with(['dokter', 'poli'])->aktif()->hariIni()->orderBy('jam_mulai')->get(),
            'logTerbaru' => ActivityLog::with('user')->latest()->limit(6)->get(),
            'jumlah' => [
                'poli' => Poli::count(),
                'layanan' => Layanan::count(),
                'dokter' => Dokter::count(),
                'pengumuman' => Pengumuman::count(),
            ],
            'rataTunggu' => $this->rataTunggu(),
        ]);
    }

    /** Rata-rata waktu tunggu (antrian dibuat → dipanggil), 30 hari terakhir. */
    private function rataTunggu(): ?int
    {
        $selisih = match (DB::connection()->getDriverName()) {
            'sqlite' => 'AVG((julianday(dipanggil_at) - julianday(created_at)) * 1440)',
            default => 'AVG(TIMESTAMPDIFF(MINUTE, created_at, dipanggil_at))',
        };

        $rata = Antrian::whereNotNull('dipanggil_at')
            // Waktu tunggu hanya bermakna bila pasien dipanggil setelah mendaftar.
            ->whereColumn('dipanggil_at', '>=', 'created_at')
            ->where('tanggal', '>=', now()->subDays(30)->toDateString())
            ->selectRaw($selisih.' as menit')
            ->value('menit');

        return $rata ? (int) round($rata) : null;
    }
}
