@extends('layouts.publik')
@section('judul', 'Layanan')

@section('konten')
<section class="grain relative overflow-hidden border-b border-line">
    <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-teal-soft/40 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
        <p class="eyebrow">Layanan kesehatan</p>
        <h1 class="mt-3 max-w-2xl text-[38px] leading-[1.08] text-ink sm:text-[48px]">
            Semua yang kami sediakan,<br><span class="italic text-teal">jelas sejak awal.</span>
        </h1>
        <p class="mt-5 max-w-xl text-[15.5px] leading-relaxed text-ink/60">
            Telusuri layanan berdasarkan poli. Setiap layanan mencantumkan ringkasan, persyaratan,
            perkiraan durasi, dan apakah membutuhkan surat rujukan.
        </p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
    {{-- Navigasi poli --}}
    <div class="sticky top-[72px] z-20 -mx-5 mb-10 border-b border-line bg-cream/90 px-5 py-3 backdrop-blur sm:mx-0 sm:rounded-2xl sm:border sm:px-4">
        <div class="flex gap-2 overflow-x-auto pb-1 sm:pb-0">
            @foreach ($polis as $p)
                <a href="#{{ $p->slug }}" class="shrink-0 rounded-full border border-line bg-white px-4 py-2 text-[13px] font-semibold text-ink/70 transition hover:border-teal hover:text-teal">
                    {{ $p->nama }}
                </a>
            @endforeach
        </div>
    </div>

    @forelse ($polis as $poli)
        <section id="{{ $poli->slug }}" class="scroll-mt-40 pb-16 last:pb-0">
            <div class="flex flex-wrap items-start justify-between gap-6 border-b border-line pb-6">
                <div class="flex items-start gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl text-white" style="background: {{ $poli->warna }}">
                        <x-ikon :nama="$poli->ikon ?: 'stethoscope'" kelas="h-6 w-6" />
                    </span>
                    <div>
                        <h2 class="text-[26px] leading-tight text-ink">{{ $poli->nama }}</h2>
                        <p class="mt-1.5 max-w-xl text-[14px] leading-relaxed text-ink/55">{{ $poli->deskripsi }}</p>
                    </div>
                </div>
                <a href="{{ route('antrian.form', ['poli' => $poli->id]) }}" class="tombol tombol-garis !min-h-10 !px-4 !py-2 !text-[13px]">
                    <x-ikon nama="ticket" kelas="h-4 w-4" /> Ambil antrian poli ini
                </a>
            </div>

            <div class="mt-7 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($poli->layanans as $l)
                    <a href="{{ route('layanan.detail', $l) }}" class="kartu kartu-naik group flex flex-col p-5">
                        <h3 class="text-[16px] font-semibold leading-snug text-ink group-hover:text-teal">{{ $l->nama }}</h3>
                        <p class="mt-2 flex-1 text-[13px] leading-relaxed text-ink/55">{{ $l->ringkasan }}</p>
                        <div class="mt-4 flex items-center justify-between border-t border-line/70 pt-3.5">
                            <span class="flex items-center gap-1.5 text-[12px] text-ink/45">
                                <x-ikon nama="clock" kelas="h-3.5 w-3.5" /> ±{{ $l->durasi_menit }} menit
                            </span>
                            @if ($l->butuh_rujukan)
                                <span class="lencana bg-coral-soft text-[#a54d24]">Rujukan</span>
                            @else
                                <span class="lencana bg-teal-soft/70 text-pine">Langsung</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-line p-6 text-center text-sm text-ink/50">
                        Belum ada layanan terdaftar di poli ini.
                    </p>
                @endforelse
            </div>
        </section>
    @empty
        <p class="rounded-2xl border border-dashed border-line p-10 text-center text-sm text-ink/50">Data layanan belum tersedia.</p>
    @endforelse
</div>
@endsection
