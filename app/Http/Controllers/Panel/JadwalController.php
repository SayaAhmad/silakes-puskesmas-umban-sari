<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Dokter;
use App\Models\Jadwal;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JadwalController extends Controller
{
    public function index()
    {
        return view('panel.jadwal', [
            'jadwal' => Jadwal::with(['dokter', 'poli'])
                ->orderBy('hari')->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari'),
            'hari' => Jadwal::HARI,
        ]);
    }

    public function create()
    {
        return view('panel.jadwal-form', [
            'jadwal' => new Jadwal(['hari' => now()->dayOfWeek, 'kuota' => 30, 'is_active' => true]),
            'dokter' => Dokter::aktif()->orderBy('nama')->get(),
            'polis' => Poli::aktif()->orderBy('urutan')->get(),
            'hari' => Jadwal::HARI,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        Jadwal::create($data);
        ActivityLog::catat('jadwal.buat', 'Jadwal', 'Menambah jadwal praktik');

        return redirect()->route('panel.jadwal.index')->with('sukses', 'Jadwal praktik ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        return view('panel.jadwal-form', [
            'jadwal' => $jadwal,
            'dokter' => Dokter::orderBy('nama')->get(),
            'polis' => Poli::orderBy('urutan')->get(),
            'hari' => Jadwal::HARI,
        ]);
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $jadwal->update($this->validasi($request, $jadwal));
        ActivityLog::catat('jadwal.ubah', 'Jadwal', 'Memperbarui jadwal praktik');

        return redirect()->route('panel.jadwal.index')->with('sukses', 'Jadwal diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        ActivityLog::catat('jadwal.hapus', 'Jadwal', 'Menghapus jadwal praktik');

        return back()->with('sukses', 'Jadwal dihapus.');
    }

    private function validasi(Request $request, ?Jadwal $jadwal = null): array
    {
        $data = $request->validate([
            'dokter_id' => ['required', 'exists:dokters,id'],
            'poli_id' => ['nullable', 'exists:polis,id'],
            'hari' => ['required', 'integer', 'between:0,6'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota' => ['required', 'integer', 'min:1', 'max:300'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'dokter_id' => 'dokter', 'poli_id' => 'poli', 'hari' => 'hari',
            'jam_mulai' => 'jam mulai', 'jam_selesai' => 'jam selesai', 'kuota' => 'kuota',
        ]);

        // Cegah jadwal bentrok untuk dokter yang sama di hari yang sama
        $bentrok = Jadwal::where('dokter_id', $data['dokter_id'])
            ->where('hari', $data['hari'])
            ->where('is_active', true)
            ->when($jadwal, fn ($q, $j) => $q->where('id', '!=', $j->id))
            ->where(fn ($q) => $q->whereBetween('jam_mulai', [$data['jam_mulai'], $data['jam_selesai']])
                ->orWhereBetween('jam_selesai', [$data['jam_mulai'], $data['jam_selesai']]))
            ->exists();

        if ($bentrok) {
            throw ValidationException::withMessages([
                'jam_mulai' => 'Dokter ini sudah punya jadwal pada jam tersebut.',
            ]);
        }

        return $data;
    }
}
