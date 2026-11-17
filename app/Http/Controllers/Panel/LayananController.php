<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Layanan;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        return view('panel.layanan', [
            'layanan' => Layanan::with('poli')
                ->when($request->query('poli'), fn ($q, $p) => $q->where('poli_id', $p))
                ->orderBy('urutan')
                ->paginate(20)
                ->withQueryString(),
            'polis' => Poli::orderBy('urutan')->get(),
            'filter' => $request->only('poli'),
        ]);
    }

    public function create()
    {
        return view('panel.layanan-form', [
            'layanan' => new Layanan(['durasi_menit' => 15, 'is_active' => true, 'urutan' => Layanan::max('urutan') + 1]),
            'polis' => Poli::orderBy('urutan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['slug'] = $this->slugUnik($data['nama']);
        $data['persyaratan'] = $this->baris($request->input('persyaratan'));

        Layanan::create($data);
        ActivityLog::catat('layanan.buat', $data['nama'], 'Menambah layanan');

        return redirect()->route('panel.layanan.index')->with('sukses', "Layanan {$data['nama']} ditambahkan.");
    }

    public function edit(Layanan $layanan)
    {
        return view('panel.layanan-form', [
            'layanan' => $layanan,
            'polis' => Poli::orderBy('urutan')->get(),
        ]);
    }

    public function update(Request $request, Layanan $layanan)
    {
        $data = $this->validasi($request);
        if ($layanan->nama !== $data['nama']) {
            $data['slug'] = $this->slugUnik($data['nama'], $layanan->id);
        }
        $data['persyaratan'] = $this->baris($request->input('persyaratan'));

        $layanan->update($data);
        ActivityLog::catat('layanan.ubah', $layanan->nama, 'Memperbarui layanan');

        return redirect()->route('panel.layanan.index')->with('sukses', "Layanan {$layanan->nama} diperbarui.");
    }

    public function destroy(Layanan $layanan)
    {
        $nama = $layanan->nama;
        $layanan->delete();
        ActivityLog::catat('layanan.hapus', $nama, 'Menghapus layanan');

        return back()->with('sukses', "Layanan {$nama} dihapus.");
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'poli_id' => ['nullable', 'exists:polis,id'],
            'nama' => ['required', 'string', 'max:100'],
            'ringkasan' => ['nullable', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string', 'max:3000'],
            'durasi_menit' => ['required', 'integer', 'min:5', 'max:240'],
            'butuh_rujukan' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'nama' => 'nama layanan', 'poli_id' => 'poli',
            'durasi_menit' => 'durasi layanan', 'urutan' => 'urutan',
        ]);
    }

    private function baris(?string $teks): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $teks))
            ->map(fn ($b) => trim($b))
            ->filter()
            ->values()
            ->all();
    }

    private function slugUnik(string $nama, ?int $kecuali = null): string
    {
        $dasar = Str::slug($nama);
        $slug = $dasar;
        $i = 1;
        while (Layanan::where('slug', $slug)->when($kecuali, fn ($q) => $q->where('id', '!=', $kecuali))->exists()) {
            $slug = $dasar . '-' . $i++;
        }

        return $slug;
    }
}
