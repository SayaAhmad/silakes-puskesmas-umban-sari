<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Faq;
use App\Models\Jadwal;
use App\Models\Layanan;
use App\Models\Pengaturan;
use App\Models\Pengumuman;
use App\Models\Poli;

class BerandaController extends Controller
{
    public function index()
    {
        $hariIni = now()->dayOfWeek;

        $jadwalHariIni = Jadwal::with(['dokter', 'poli'])
            ->aktif()
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        $antrianBerjalan = Antrian::with('poli')
            ->hariIni()
            ->whereIn('status', ['dipanggil', 'menunggu'])
            ->orderBy('status')
            ->orderBy('nomor')
            ->get()
            ->groupBy('poli_id');

        return view('publik.beranda', [
            'polis' => Poli::aktif()->urut()->get(),
            'layananUnggulan' => Layanan::with('poli')->aktif()->orderBy('urutan')->limit(6)->get(),
            'jadwalHariIni' => $jadwalHariIni,
            'antrianBerjalan' => $antrianBerjalan,
            'pengumuman' => Pengumuman::terbit()->latest('published_at')->limit(3)->get(),
            'faqSingkat' => Faq::aktif()->limit(4)->get(),
            'totalLayanan' => Layanan::aktif()->count(),
            'totalDokter' => Dokter::aktif()->count(),
            'antrianHariIni' => Antrian::hariIni()->count(),
            'statusBuka' => $this->statusBuka(),
            'jamOperasional' => Pengaturan::get('jam_operasional', 'Senin–Sabtu, 08.00–14.00'),
        ]);
    }

    /** Apakah sekarang di dalam jam operasional puskesmas. */
    public static function statusBuka(): bool
    {
        $buka = Pengaturan::get('jam_buka', '08:00');
        $tutup = Pengaturan::get('jam_tutup', '14:00');
        $hariKerja = Pengaturan::get('hari_kerja', '1,2,3,4,5,6');

        if (! in_array((string) now()->dayOfWeek, self::daftarHari($hariKerja), true)) {
            return false;
        }

        $sekarang = now()->format('H:i');

        return $sekarang >= $buka && $sekarang <= $tutup;
    }

    /**
     * Normalisasi hari kerja: terima "1,2,3" maupun "Senin–Jumat".
     *
     * @return array<int, string>
     */
    public static function daftarHari(string $teks): array
    {
        $teks = trim($teks);

        if ($teks === '') {
            return [];
        }

        // Sudah berupa angka (0=Minggu ... 6=Sabtu).
        if (preg_match('/^[0-6](\s*[-,]\s*[0-6])*$/', $teks)) {
            $hasil = [];
            foreach (explode(',', $teks) as $bagian) {
                $bagian = trim($bagian);
                if (str_contains($bagian, '-')) {
                    [$awal, $akhir] = array_map('intval', explode('-', $bagian));
                    for ($i = $awal; $i <= $akhir; $i++) {
                        $hasil[] = (string) $i;
                    }
                } else {
                    $hasil[] = (string) (int) $bagian;
                }
            }

            return array_values(array_unique($hasil));
        }

        // Berupa nama hari, mis. "Senin–Jumat" atau "Senin, Rabu, Sabtu".
        $nama = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $teks = strtolower(str_replace(['–', '—', 's/d', 's.d.'], '-', $teks));

        $hasil = [];
        foreach (preg_split('/\s*,\s*/', $teks) as $bagian) {
            if (str_contains($bagian, '-')) {
                [$awal, $akhir] = array_map('trim', explode('-', $bagian, 2));
                $i = array_search($awal, $nama, true);
                $j = array_search($akhir, $nama, true);
                if ($i !== false && $j !== false) {
                    for ($k = $i; $k <= $j; $k++) {
                        $hasil[] = (string) $k;
                    }
                }
                continue;
            }

            $i = array_search(trim($bagian), $nama, true);
            if ($i !== false) {
                $hasil[] = (string) $i;
            }
        }

        return array_values(array_unique($hasil));
    }
}
