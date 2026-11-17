<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PoliController extends Controller
{
    public function index()
    {
        return view('panel.poli', [
            'polis' => Poli::withCount(['layanans', 'dokters'])->orderBy('urutan')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('panel.poli-form', ['poli' => new Poli(['urutan' => Poli::max('urutan') + 1, 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['slug'] = $this->slugUnik($data['nama']);

        Poli::create($data);
        ActivityLog::catat('poli.buat', $data['nama'], 'Menambah poli baru');

        return redirect()->route('panel.poli.index')->with('sukses', "Poli {$data['nama']} ditambahkan.");
    }

    public function edit(Poli $poli)
    {
        return view('panel.poli-form', ['poli' => $poli]);
    }

    public function update(Request $request, Poli $poli)
    {
        $data = $this->validasi($request, $poli->id);
        if ($poli->nama !== $data['nama']) {
            $data['slug'] = $this->slugUnik($data['nama'], $poli->id);
        }

        $poli->update($data);
        ActivityLog::catat('poli.ubah', $poli->nama, 'Memperbarui data poli');

        return redirect()->route('panel.poli.index')->with('sukses', "Poli {$poli->nama} diperbarui.");
    }

    public function destroy(Poli $poli)
    {
        if ($poli->antrians()->exists()) {
            return back()->with('info', 'Poli ini punya riwayat antrian. Nonaktifkan saja agar riwayat tetap utuh.');
        }

        $nama = $poli->nama;
        $poli->delete();
        ActivityLog::catat('poli.hapus', $nama, 'Menghapus poli');

        return back()->with('sukses', "Poli {$nama} dihapus.");
    }

    private function validasi(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:80'],
            'ikon' => ['nullable', 'string', 'max:40'],
            'warna' => ['required', 'string', 'max:9'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['nama' => 'nama poli', 'warna' => 'warna', 'urutan' => 'urutan']);
    }

    private function slugUnik(string $nama, ?int $kecuali = null): string
    {
        $dasar = Str::slug($nama);
        $slug = $dasar;
        $i = 1;
        while (Poli::where('slug', $slug)->when($kecuali, fn ($q) => $q->where('id', '!=', $kecuali))->exists()) {
            $slug = $dasar . '-' . $i++;
        }

        return $slug;
    }
}
