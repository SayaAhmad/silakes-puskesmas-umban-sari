@extends('layouts.publik')
@section('judul', 'Pengumuman')

@section('konten')
<section class="border-b border-line">
    <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
        <p class="eyebrow">Kabar & informasi</p>
        <h1 class="mt-3 max-w-2xl text-[38px] leading-[1.08] text-ink sm:text-[48px]">Pengumuman</h1>
        <p class="mt-5 max-w-xl text-[15.5px] leading-relaxed text-ink/60">
            Perubahan jadwal, program kesehatan, dan informasi layanan terbaru dari Puskesmas Umban Sari.
        </p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($pengumuman as $p)
            <a href="{{ route('pengumuman.detail', $p) }}" class="kartu kartu-naik group flex flex-col p-6">
                <div class="flex items-center justify-between">
                    <span class="lencana bg-teal-soft/70 text-pine">{{ $p->labelKategori() }}</span>
                    <span class="text-[11.5px] text-ink/40">{{ $p->published_at?->translatedFormat('d M Y') }}</span>
                </div>
                <h2 class="mt-4 text-[18px] font-semibold leading-snug text-ink group-hover:text-teal">{{ $p->judul }}</h2>
                <p class="mt-2 flex-1 line-clamp-3 text-[13.5px] leading-relaxed text-ink/55">{{ $p->ringkasan }}</p>
                <div class="mt-5 flex items-center gap-1.5 border-t border-line/70 pt-4 text-[12.5px] font-semibold text-teal">
                    Baca selengkapnya
                    <x-ikon nama="arrow-right" kelas="h-3.5 w-3.5 transition group-hover:translate-x-0.5" />
                </div>
            </a>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-line p-12 text-center text-sm text-ink/50">
                Belum ada pengumuman yang dipublikasikan.
            </p>
        @endforelse
    </div>

    @if ($pengumuman->hasPages())
        <div class="mt-10">{{ $pengumuman->links() }}</div>
    @endif
</div>
@endsection
