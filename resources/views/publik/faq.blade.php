@extends('layouts.publik')
@section('judul', 'Pertanyaan Umum')

@section('konten')
<section class="border-b border-line">
    <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
        <p class="eyebrow">Bantuan</p>
        <h1 class="mt-3 max-w-2xl text-[38px] leading-[1.08] text-ink sm:text-[48px]">
            Pertanyaan yang<br><span class="italic text-teal">sering diajukan.</span>
        </h1>
    </div>
</section>

<div class="mx-auto max-w-4xl px-5 py-14 sm:px-8">
    @forelse ($faq as $kategori => $daftar)
        <section class="mb-10 last:mb-0">
            <h2 class="text-[13px] font-bold uppercase tracking-[0.14em] text-teal">{{ $labelKategori[$kategori] ?? ucfirst($kategori) }}</h2>

            <div class="mt-4 space-y-2.5" x-data="{ buka: null }">
                @foreach ($daftar as $i => $f)
                    <div class="kartu overflow-hidden">
                        <button @click="buka = buka === '{{ $kategori }}{{ $i }}' ? null : '{{ $kategori }}{{ $i }}'"
                                class="flex w-full items-center gap-4 px-5 py-4 text-left">
                            <span class="flex-1 text-[14.5px] font-semibold leading-snug text-ink">{{ $f->pertanyaan }}</span>
                            <x-ikon nama="plus" kelas="h-4 w-4 shrink-0 text-ink/35 transition" x-bind:class="buka === '{{ $kategori }}{{ $i }}' && 'rotate-45'" />
                        </button>
                        <div x-show="buka === '{{ $kategori }}{{ $i }}'" x-transition x-cloak>
                            <p class="border-t border-line/70 px-5 py-4 text-[14px] leading-[1.75] text-ink/60">{{ $f->jawaban }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @empty
        <p class="rounded-2xl border border-dashed border-line p-12 text-center text-sm text-ink/50">
            Belum ada pertanyaan yang dipublikasikan.
        </p>
    @endforelse

    <div class="mt-12 flex flex-col items-start gap-4 rounded-2xl bg-ink p-7 sm:flex-row sm:items-center">
        <div class="flex-1">
            <p class="font-display text-[20px] text-white">Masih ada yang ingin ditanyakan?</p>
            <p class="mt-1.5 text-[13.5px] text-white/50">Hubungi petugas kami pada jam layanan.</p>
        </div>
        <a href="{{ route('kontak') }}" class="tombol bg-cream text-ink hover:bg-white !py-2.5 !text-[13.5px]">Hubungi kami</a>
    </div>
</div>
@endsection
