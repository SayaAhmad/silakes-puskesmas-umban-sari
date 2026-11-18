<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Antrian;
use App\Models\Jadwal;
use App\Models\Pengaturan;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AntrianController extends Controller
{
    public function form(Request $request)
    {
        return view('publik.antrian-form', [
            'polis' => Poli::aktif()->urut()->with(['layanans' => fn ($q) => $q->aktif()])->get(),
            'pilihan' => $request->query('poli'),
            'poliBukaHariIni' => Jadwal::aktif()->where('hari', now()->dayOfWeek)->pluck('poli_id')->unique()->all(),
            'antrianHariIni' => Antrian::hariIni()
                ->selectRaw('poli_id, count(*) as jumlah')
                ->groupBy('poli_id')
                ->pluck('jumlah', 'poli_id'),
        ]);
    }

    public function simpan(Request $request)
    {
        $data = $request->validate([
            'poli_id' => ['required', 'exists:polis,id'],
            'nama_pasien' => ['required', 'string', 'max:100'],
            'nik' => ['nullable', 'digits:16'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'keluhan' => ['nullable', 'string', 'max:500'],
        ], [], [
            'poli_id' => 'poli tujuan',
            'nama_pasien' => 'nama lengkap',
            'jenis_kelamin' => 'jenis kelamin',
        ]);

        $poli = Poli::aktif()->findOrFail($data['poli_id']);
        $tanggal = now()->toDateString();

        // Antrian online tutup di luar jam operasional
        if ($this->diLuarJamOperasional()) {
            $buka = Pengaturan::get('jam_buka');
            $tutup = Pengaturan::get('jam_tutup');

            throw ValidationException::withMessages([
                'poli_id' => "Antrian online hanya dibuka pukul {$buka}–{$tutup} WIB. Silakan ambil nomor langsung di loket atau kembali pada jam layanan.",
            ]);
        }

        // Cek jadwal poli hari ini
        $adaJadwal = Jadwal::aktif()->where('poli_id', $poli->id)->where('hari', now()->dayOfWeek)->exists();
        if (! $adaJadwal) {
            throw ValidationException::withMessages([
                'poli_id' => "Poli {$poli->nama} tidak membuka layanan hari ini. Silakan pilih poli lain atau ambil nomor pada hari praktik.",
            ]);
        }

        $antrian = DB::transaction(function () use ($data, $poli, $tanggal) {
            // Anti-dobel: satu NIK hanya boleh punya satu antrian aktif per hari per poli
            if (! empty($data['nik']) && Antrian::where('poli_id', $poli->id)
                ->whereDate('tanggal', $tanggal)
                ->where('nik', $data['nik'])
                ->whereIn('status', ['menunggu', 'dipanggil'])
                ->exists()) {
                throw ValidationException::withMessages([
                    'nik' => 'NIK ini sudah punya antrian aktif di poli ini hari ini.',
                ]);
            }

            $nomor = Antrian::nomorBerikutnya($poli->id, $tanggal);

            return Antrian::create([
                'tanggal' => $tanggal,
                'poli_id' => $poli->id,
                'nomor' => $nomor,
                'kode' => Antrian::generateKodeUnik($poli, $nomor),
                'nama_pasien' => $data['nama_pasien'],
                'nik' => $data['nik'] ?? null,
                'no_hp' => $data['no_hp'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'],
                'keluhan' => $data['keluhan'] ?? null,
                'status' => 'menunggu',
                'sumber' => 'online',
            ]);
        });

        return redirect()->route('antrian.tiket', $antrian->kode)
            ->with('sukses', 'Nomor antrian Anda berhasil dibuat.');
    }

    public function tiket(string $kode)
    {
        $antrian = Antrian::with('poli')->where('kode', $kode)->firstOrFail();

        $didepan = Antrian::where('poli_id', $antrian->poli_id)
            ->whereDate('tanggal', $antrian->tanggal)
            ->where('nomor', '<', $antrian->nomor)
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->count();

        return view('publik.antrian-tiket', [
            'antrian' => $antrian,
            'didepan' => $didepan,
            'estimasi' => $antrian->estimasi(),
            'sedangDipanggil' => Antrian::where('poli_id', $antrian->poli_id)
                ->whereDate('tanggal', $antrian->tanggal)
                ->where('status', 'dipanggil')
                ->orderBy('dipanggil_at')
                ->first(),
        ]);
    }

    public function layar()
    {
        return view('publik.antrian-layar', [
            'polis' => Poli::aktif()->urut()->get(),
        ]);
    }

    /** Data JSON untuk layar & tiket (polling). */
    public function statusJson()
    {
        $hariIni = now()->toDateString();

        $dipanggil = Antrian::with('poli')
            ->whereDate('tanggal', $hariIni)
            ->where('status', 'dipanggil')
            ->orderByDesc('dipanggil_at')
            ->get();

        $menunggu = Antrian::with('poli')
            ->whereDate('tanggal', $hariIni)
            ->where('status', 'menunggu')
            ->orderBy('nomor')
            ->limit(30)
            ->get();

        $terakhir = $dipanggil->first();

        return response()->json([
            'waktu' => now()->format('H:i:s'),
            'tanggal' => now()->translatedFormat('l, d F Y'),
            'terakhir' => $terakhir ? [
                'kode' => $terakhir->kode,
                'nomor' => $terakhir->nomor,
                'poli' => $terakhir->poli->nama,
                'nama' => $terakhir->nama_pasien,
                'dipanggil_at' => $terakhir->dipanggil_at?->format('H:i'),
            ] : null,
            'menunggu' => $menunggu->map(fn ($a) => [
                'kode' => $a->kode,
                'nomor' => $a->nomor,
                'poli' => $a->poli->nama,
            ])->values(),
            'per_poli' => Poli::aktif()->urut()->get()->map(function ($p) use ($hariIni) {
                $dipanggil = Antrian::whereDate('tanggal', $hariIni)
                    ->where('poli_id', $p->id)->where('status', 'dipanggil')
                    ->orderByDesc('dipanggil_at')->first();
                $menunggu = Antrian::whereDate('tanggal', $hariIni)
                    ->where('poli_id', $p->id)->where('status', 'menunggu')->count();

                $berikutnya = Antrian::whereDate('tanggal', $hariIni)
                    ->where('poli_id', $p->id)->where('status', 'menunggu')
                    ->orderBy('nomor')->first();

                return [
                    'poli' => $p->nama,
                    'warna' => $p->warna,
                    'sekarang' => $dipanggil?->kode,
                    'berikutnya' => $berikutnya?->kode,
                    'menunggu' => $menunggu,
                ];
            })->values(),
        ]);
    }

    /** True bila waktu server di luar jam buka pengaturan (PRD F-05). */
    private function diLuarJamOperasional(): bool
    {
        $buka = Pengaturan::get('jam_buka');
        $tutup = Pengaturan::get('jam_tutup');

        if (blank($buka) || blank($tutup)) {
            return false;
        }

        $sekarang = now();

        return $sekarang->lt($sekarang->copy()->setTimeFromTimeString($buka))
            || $sekarang->gt($sekarang->copy()->setTimeFromTimeString($tutup));
    }
}
