<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index()
    {
        return view('panel.pengumuman', [
            'pengumuman' => Pengumuman::with('user')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('panel.pengumuman-form', [
            'pengumuman' => new Pengumuman(['is_published' => true, 'kategori' => 'umum']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        $data['user_id'] = auth()->id();

        $p = Pengumuman::create($data);
        ActivityLog::catat('pengumuman.buat', $p->judul, 'Membuat pengumuman');

        return redirect()->route('panel.pengumuman.index')->with('sukses', 'Pengumuman disimpan.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('panel.pengumuman-form', ['pengumuman' => $pengumuman]);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $pengumuman->update($this->validasi($request));
        ActivityLog::catat('pengumuman.ubah', $pengumuman->judul, 'Memperbarui pengumuman');

        return redirect()->route('panel.pengumuman.index')->with('sukses', 'Pengumuman diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $judul = $pengumuman->judul;
        $pengumuman->delete();
        ActivityLog::catat('pengumuman.hapus', $judul, 'Menghapus pengumuman');

        return back()->with('sukses', 'Pengumuman dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'in:umum,jadwal,kesehatan,layanan'],
            'ringkasan' => ['nullable', 'string', 'max:300'],
            'isi' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ], [], ['judul' => 'judul', 'isi' => 'isi pengumuman', 'kategori' => 'kategori']);
    }
}
