@extends('layouts.panel')
@section('judul', 'FAQ')
@section('remah', 'Konten · Pertanyaan umum')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Pertanyaan umum</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Jawaban singkat untuk pertanyaan yang sering muncul.</p>
    </div>
    <a href="{{ route('panel.faq.create') }}" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
        <x-ikon nama="plus" kelas="h-4 w-4" /> Tambah FAQ
    </a>
</div>

<div class="kartu mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Pertanyaan</th>
                    <th>Kategori</th>
                    <th class="text-center">Urut</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($faq as $f)
                    <tr>
                        <td>
                            <p class="max-w-[420px] font-medium text-ink">{{ $f->pertanyaan }}</p>
                            <p class="max-w-[420px] truncate text-[11.5px] text-ink/40">{{ $f->jawaban }}</p>
                        </td>
                        <td>
                            <span class="lencana bg-sand text-ink/60">{{ $labelKategori[$f->kategori] ?? $f->kategori }}</span>
                        </td>
                        <td class="text-center text-ink/40">{{ $f->urutan }}</td>
                        <td>
                            @if ($f->is_active)
                                <span class="lencana bg-teal-soft text-pine">Aktif</span>
                            @else
                                <span class="lencana bg-sand text-ink/45">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('panel.faq.edit', $f) }}" class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Ubah">
                                    <x-ikon nama="edit" kelas="h-4 w-4" />
                                </a>
                                <form method="POST" action="{{ route('panel.faq.destroy', $f) }}" onsubmit="return confirm('Hapus FAQ ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-ink/25 transition hover:bg-coral-soft hover:text-coral" title="Hapus">
                                        <x-ikon nama="trash" kelas="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-14 text-center">
                            <p class="text-[13.5px] text-ink/45">Belum ada FAQ.</p>
                            <a href="{{ route('panel.faq.create') }}" class="mt-2 inline-block text-[13px] font-semibold text-teal hover:underline">Tambah FAQ pertama</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($faq->hasPages())
    <div class="mt-5">{{ $faq->links() }}</div>
@endif
@endsection
