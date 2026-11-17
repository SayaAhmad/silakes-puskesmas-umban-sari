@extends('layouts.publik')
@section('judul', $pengumuman->judul)

@section('konten')
<div class="mx-auto max-w-3xl px-5 py-10 sm:px-8">
    <nav class="flex flex-wrap items-center gap-2 text-[12.5px] text-ink/45">
        <a href="{{ route('beranda') }}" class="hover:text-teal">Beranda</a>
        <span>/</span>
        <a href="{{ route('pengumuman.index') }}" class="hover:text-teal">Pengumuman</a>
    </nav>

    <article class="mt-8">
        <div class="flex flex-wrap items-center gap-3">
            <span class="lencana bg-teal-soft/70 text-pine">{{ $pengumuman->labelKategori() }}</span>
            <span class="text-[12px] text-ink/45">{{ $pengumuman->published_at?->translatedFormat('l, d F Y') }}</span>
        </div>

        <h1 class="mt-5 text-[34px] leading-[1.12] text-ink sm:text-[42px]">{{ $pengumuman->judul }}</h1>

        @if ($pengumuman->ringkasan)
            <p class="mt-5 border-l-2 border-teal/40 pl-5 text-[16px] italic leading-relaxed text-ink/60">
                {{ $pengumuman->ringkasan }}
            </p>
        @endif

        <div class="mt-8 space-y-4 text-[15.5px] leading-[1.85] text-ink/75">
            @foreach (preg_split('/\n{2,}/', trim($pengumuman->isi)) as $paragraf)
                <p>{{ $paragraf }}</p>
            @endforeach
        </div>

        <div class="mt-10 flex items-center gap-3 border-t border-line pt-6">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-ink/[0.04] font-display text-[13px] font-semibold text-ink/60">
                {{ $pengumuman->user?->name ? strtoupper(substr($pengumuman->user->name, 0, 2)) : 'PS' }}
            </span>
            <div>
                <p class="text-[13px] font-semibold text-ink">{{ $pengumuman->user->name ?? 'Puskesmas Umban Sari' }}</p>
                <p class="text-[11.5px] text-ink/45">{{ $pengumuman->user->jabatan ?? 'Admin puskesmas' }}</p>
            </div>
        </div>
    </article>

    @if ($lainnya->isNotEmpty())
        <div class="mt-14 border-t border-line pt-8">
            <h2 class="text-[13px] font-bold uppercase tracking-[0.12em] text-ink/45">Pengumuman lainnya</h2>
            <div class="mt-4 space-y-2.5">
                @foreach ($lainnya as $l)
                    <a href="{{ route('pengumuman.detail', $l) }}" class="kartu kartu-naik group flex items-center gap-4 p-4">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-sand text-ink/50">
                            <x-ikon nama="megaphone" kelas="h-4 w-4" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[14px] font-semibold text-ink group-hover:text-teal">{{ $l->judul }}</p>
                            <p class="text-[11.5px] text-ink/40">{{ $l->published_at?->translatedFormat('d M Y') }}</p>
                        </div>
                        <x-ikon nama="arrow-right" kelas="h-4 w-4 shrink-0 text-ink/25 transition group-hover:translate-x-0.5 group-hover:text-teal" />
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mt-10">
        <a href="{{ route('pengumuman.index') }}" class="inline-flex items-center gap-2 text-[13.5px] font-semibold text-teal hover:underline">
            <x-ikon nama="arrow-left" kelas="h-4 w-4" /> Semua pengumuman
        </a>
    </div>
</div>
@endsection
