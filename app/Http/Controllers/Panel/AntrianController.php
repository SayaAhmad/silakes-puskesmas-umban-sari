<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Antrian;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AntrianController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? \Illuminate\Support\Carbon::parse($request->query('tanggal'))->toDateString()
            : now()->toDateString();

        $query = Antrian::with('poli')
            ->whereDate('tanggal', $tanggal)
            ->when($request->query('poli'), fn ($q, $poli) => $q->where('poli_id', $poli))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('cari'), function ($q, $cari) {
                $q->where(fn ($w) => $w->where('nama_pasien', 'like', "%{$cari}%")
                    ->orWhere('kode', 'like', "%{$cari}%"));
            })
            ->orderByRaw("FIELD(status, 'dipanggil', 'menunggu', 'selesai', 'lewat', 'batal')")
            ->orderBy('nomor');

        return view('panel.antrian', [
            'antrian' => $query->paginate(25)->withQueryString(),
            'polis' => Poli::aktif()->urut()->get(),
            'tanggal' => $tanggal,
            'status' => Antrian::STATUS,
            'ringkasan' => [
                'total' => Antrian::whereDate('tanggal', $tanggal)->count(),
                'menunggu' => Antrian::whereDate('tanggal', $tanggal)->where('status', 'menunggu')->count(),
                'dipanggil' => Antrian::whereDate('tanggal', $tanggal)->where('status', 'dipanggil')->count(),
                'selesai' => Antrian::whereDate('tanggal', $tanggal)->where('status', 'selesai')->count(),
            ],
            'filter' => $request->only(['poli', 'status', 'cari']),
        ]);
    }

    /** Petugas mendaftarkan pasien langsung di loket. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'poli_id' => ['required', 'exists:polis,id'],
            'nama_pasien' => ['required', 'string', 'max:100'],
            'nik' => ['nullable', 'digits:16'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'keluhan' => ['nullable', 'string', 'max:500'],
        ], [], ['poli_id' => 'poli', 'nama_pasien' => 'nama pasien', 'jenis_kelamin' => 'jenis kelamin']);

        $poli = Poli::findOrFail($data['poli_id']);
        $tanggal = now()->toDateString();

        $antrian = DB::transaction(function () use ($data, $poli, $tanggal) {
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
                'sumber' => 'loket',
            ]);
        });

        ActivityLog::catat('antrian.daftar', $antrian->kode, 'Pendaftaran di loket: ' . $antrian->nama_pasien);

        return back()->with('sukses', "Antrian {$antrian->kode} ({$antrian->nama_pasien}) ditambahkan.");
    }

    public function panggil(Antrian $antrian)
    {
        $antrian->update([
            'status' => 'dipanggil',
            'dipanggil_at' => now(),
        ]);

        ActivityLog::catat('antrian.panggil', $antrian->kode, 'Memanggil ' . $antrian->nama_pasien);

        return back()->with('sukses', "Memanggil {$antrian->kode} — {$antrian->nama_pasien}.");
    }

    public function panggilBerikutnya(Request $request)
    {
        $poliId = $request->input('poli_id');

        $berikut = Antrian::hariIni()
            ->where('status', 'menunggu')
            ->when($poliId, fn ($q, $id) => $q->where('poli_id', $id))
            ->orderBy('nomor')
            ->first();

        if (! $berikut) {
            return back()->with('info', 'Tidak ada antrian yang menunggu.');
        }

        return $this->panggil($berikut);
    }

    public function selesai(Antrian $antrian)
    {
        $antrian->update([
            'status' => 'selesai',
            'selesai_at' => now(),
            'dipanggil_at' => $antrian->dipanggil_at ?? now(),
        ]);

        ActivityLog::catat('antrian.selesai', $antrian->kode, 'Menyelesaikan layanan ' . $antrian->nama_pasien);

        return back()->with('sukses', "Antrian {$antrian->kode} selesai.");
    }

    public function lewat(Antrian $antrian)
    {
        $antrian->update(['status' => 'lewat']);
        ActivityLog::catat('antrian.lewat', $antrian->kode, 'Antrian terlewat: ' . $antrian->nama_pasien);

        return back()->with('sukses', "Antrian {$antrian->kode} ditandai terlewat.");
    }

    public function batal(Antrian $antrian)
    {
        $antrian->update(['status' => 'batal']);
        ActivityLog::catat('antrian.batal', $antrian->kode, 'Antrian dibatalkan: ' . $antrian->nama_pasien);

        return back()->with('sukses', "Antrian {$antrian->kode} dibatalkan.");
    }
}
