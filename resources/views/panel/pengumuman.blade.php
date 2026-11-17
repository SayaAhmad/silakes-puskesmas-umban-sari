@extends('layouts.panel')
@section('judul', 'Pengumuman')
@section('remah', 'Konten · Pengumuman')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Pengumuman</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Informasi yang tampil di situs publik dan beranda.</p>
    </div>
    <a href="{{ route('panel.pengumuman.create') }}" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
        <x-ikon nama="plus" kelas="h-4 w-4" /> Tulis pengumuman
    </a>
</div>

<div class="mt-5 space-y-3">
    @forelse ($pengumuman as $p)
        <div class="kartu flex flex-wrap items-start gap-4 p-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-teal-soft/60 text-teal">
                <x-ikon nama="megaphone" kelas="h-[18px] w-[18px]" />
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="lencana bg-sand text-ink/60">{{ $p->labelKategori() }}</span>
                    @if ($p->is_published)
                        <span class="lencana bg-teal-soft text-pine">Terbit</span>
                    @else
                        <span class="lencana bg-coral-soft text-[#a54d24]">Draf</span>
                    @endif
                    <span class="text-[11.5px] text-ink/35">
                        {{ $p->published_at?->translatedFormat('d M Y') ?? 'Belum dijadwalkan' }}
                    </span>
                </div>
                <p class="mt-2 text-[14.5px] font-semibold leading-snug text-ink">{{ $p->judul }}</p>
                @if ($p->ringkasan)
                    <p class="mt-1 line-clamp-2 text-[12.5px] leading-relaxed text-ink/50">{{ $p->ringkasan }}</p>
                @endif
                <p class="mt-2 text-[11.5px] text-ink/35">Oleh {{ $p->user->name ?? 'Sistem' }}</p>
            </div>

            <div class="flex items-center gap-1">
                @if ($p->is_published)
                    <a href="{{ route('pengumuman.detail', $p) }}" target="_blank"
                       class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Lihat di situs">
                        <x-ikon nama="arrow-up-right" kelas="h-4 w-4" />
                    </a>
                @endif
                <a href="{{ route('panel.pengumuman.edit', $p) }}" class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Ubah">
                    <x-ikon nama="edit" kelas="h-4 w-4" />
                </a>
                <form method="POST" action="{{ route('panel.pengumuman.destroy', $p) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-ink/25 transition hover:bg-coral-soft hover:text-coral" title="Hapus">
                        <x-ikon nama="trash" kelas="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-dashed border-line p-14 text-center">
            <p class="text-[13.5px] text-ink/45">Belum ada pengumuman.</p>
            <a href="{{ route('panel.pengumuman.create') }}" class="mt-2 inline-block text-[13px] font-semibold text-teal hover:underline">Tulis pengumuman pertama</a>
        </div>
    @endforelse
</div>

@if ($pengumuman->hasPages())
    <div class="mt-5">{{ $pengumuman->links() }}</div>
@endif
@endsection
