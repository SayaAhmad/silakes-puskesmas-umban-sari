<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Dokter;
use App\Models\Poli;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        return view('panel.dokter', [
            'dokter' => Dokter::with('poli')
                ->when($request->query('poli'), fn ($q, $p) => $q->where('poli_id', $p))
                ->orderBy('urutan')->paginate(20)->withQueryString(),
            'polis' => Poli::orderBy('urutan')->get(),
            'filter' => $request->only('poli'),
        ]);
    }

    public function create()
    {
        return view('panel.dokter-form', [
            'dokter' => new Dokter(['is_active' => true, 'urutan' => Dokter::max('urutan') + 1]),
            'polis' => Poli::orderBy('urutan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        Dokter::create($data);
        ActivityLog::catat('dokter.buat', $data['nama'], 'Menambah tenaga kesehatan');

        return redirect()->route('panel.dokter.index')->with('sukses', "Tenaga kesehatan {$data['nama']} ditambahkan.");
    }

    public function edit(Dokter $dokter)
    {
        return view('panel.dokter-form', [
            'dokter' => $dokter,
            'polis' => Poli::orderBy('urutan')->get(),
        ]);
    }

    public function update(Request $request, Dokter $dokter)
    {
        $dokter->update($this->validasi($request));
        ActivityLog::catat('dokter.ubah', $dokter->nama, 'Memperbarui data tenaga kesehatan');

        return redirect()->route('panel.dokter.index')->with('sukses', "Data {$dokter->nama} diperbarui.");
    }

    public function destroy(Dokter $dokter)
    {
        if ($dokter->jadwals()->exists()) {
            return back()->with('info', 'Dokter ini masih punya jadwal praktik. Hapus jadwalnya dulu.');
        }

        $nama = $dokter->nama;
        $dokter->delete();
        ActivityLog::catat('dokter.hapus', $nama, 'Menghapus tenaga kesehatan');

        return back()->with('sukses', "{$nama} dihapus.");
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'poli_id' => ['nullable', 'exists:polis,id'],
            'nama' => ['required', 'string', 'max:100'],
            'gelar' => ['nullable', 'string', 'max:60'],
            'sip' => ['nullable', 'string', 'max:60'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['nama' => 'nama', 'poli_id' => 'poli', 'sip' => 'SIP', 'urutan' => 'urutan']);
    }
}
