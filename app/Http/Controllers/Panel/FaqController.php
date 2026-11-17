<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return view('panel.faq', [
            'faq' => Faq::orderBy('kategori')->orderBy('urutan')->paginate(20),
            'labelKategori' => Faq::KATEGORI,
        ]);
    }

    public function create()
    {
        return view('panel.faq-form', [
            'faq' => new Faq(['is_active' => true, 'urutan' => Faq::max('urutan') + 1, 'kategori' => 'umum']),
            'labelKategori' => Faq::KATEGORI,
        ]);
    }

    public function store(Request $request)
    {
        Faq::create($this->validasi($request));
        ActivityLog::catat('faq.buat', 'FAQ', 'Menambah pertanyaan umum');

        return redirect()->route('panel.faq.index')->with('sukses', 'FAQ ditambahkan.');
    }

    public function edit(Faq $faq)
    {
        return view('panel.faq-form', ['faq' => $faq, 'labelKategori' => Faq::KATEGORI]);
    }

    public function update(Request $request, Faq $faq)
    {
        $faq->update($this->validasi($request));
        ActivityLog::catat('faq.ubah', 'FAQ', 'Memperbarui FAQ');

        return redirect()->route('panel.faq.index')->with('sukses', 'FAQ diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        ActivityLog::catat('faq.hapus', 'FAQ', 'Menghapus FAQ');

        return back()->with('sukses', 'FAQ dihapus.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'pertanyaan' => ['required', 'string', 'max:200'],
            'jawaban' => ['required', 'string', 'max:2000'],
            'kategori' => ['required', 'in:umum,pendaftaran,layanan,biaya'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], ['pertanyaan' => 'pertanyaan', 'jawaban' => 'jawaban', 'kategori' => 'kategori']);
    }
}
