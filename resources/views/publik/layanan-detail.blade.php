@extends('layouts.publik')
@section('judul', $layanan->nama)

@section('konten')
<div class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
    <nav class="flex flex-wrap items-center gap-2 text-[12.5px] text-ink/45">
        <a href="{{ route('beranda') }}" class="hover:text-teal">Beranda</a>
        <span>/</span>
        <a href="{{ route('layanan.index') }}" class="hover:text-teal">Layanan</a>
        <span>/</span>
        <span class="text-ink/70">{{ $layanan->nama }}</span>
    </nav>

    <div class="mt-8 grid gap-12 lg:grid-cols-[1.4fr_0.6fr]">
        <article>
            <div class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $layanan->poli->warna ?? '#12796A' }}"></span>
                <span class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/45">{{ $layanan->poli->nama ?? 'Layanan' }}</span>
            </div>
            <h1 class="mt-3 text-[36px] leading-[1.1] text-ink sm:text-[44px]">{{ $layanan->nama }}</h1>
            <p class="mt-4 max-w-2xl text-[16px] leading-relaxed text-ink/60">{{ $layanan->ringkasan }}</p>

            <div class="mt-8 flex flex-wrap gap-3">
                <span class="lencana bg-sand text-ink/60"><x-ikon nama="clock" kelas="h-3.5 w-3.5" /> Perkiraan ±{{ $layanan->durasi_menit }} menit</span>
                @if ($layanan->butuh_rujukan)
                    <span class="lencana bg-coral-soft text-[#a54d24]"><x-ikon nama="clipboard" kelas="h-3.5 w-3.5" /> Perlu surat rujukan</span>
                @else
                    <span class="lencana bg-teal-soft text-pine"><x-ikon nama="check-bulat" kelas="h-3.5 w-3.5" /> Bisa langsung datang</span>
                @endif
            </div>

            @if ($layanan->deskripsi)
                <div class="prose-puskesmas mt-9 max-w-2xl space-y-4 text-[15px] leading-[1.8] text-ink/70">
                    @foreach (preg_split('/\n{2,}/', trim($layanan->deskripsi)) as $paragraf)
                        <p>{{ $paragraf }}</p>
                    @endforeach
                </div>
            @endif

            @if (! empty($layanan->persyaratan))
                <div class="mt-10 rounded-2xl border border-line bg-white p-6">
                    <h2 class="flex items-center gap-2 text-[17px] font-semibold text-ink">
                        <x-ikon nama="clipboard" kelas="h-[18px] w-[18px] text-teal" /> Yang perlu dibawa
                    </h2>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($layanan->persyaratan as $syarat)
                            <li class="flex items-start gap-3 text-[14px] leading-relaxed text-ink/65">
                                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-teal-soft text-pine">
                                    <x-ikon nama="check" kelas="h-3 w-3" />
                                </span>
                                {{ $syarat }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($lainnya->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-[13px] font-bold uppercase tracking-[0.12em] text-ink/45">Layanan lain di poli ini</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($lainnya as $l)
                            <a href="{{ route('layanan.detail', $l) }}" class="kartu kartu-naik group p-4">
                                <p class="text-[14.5px] font-semibold text-ink group-hover:text-teal">{{ $l->nama }}</p>
                                <p class="mt-1 line-clamp-1 text-[12.5px] text-ink/50">{{ $l->ringkasan }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-[26px] bg-ink p-7 text-white">
                <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-teal-soft/60">Siap berkunjung?</p>
                <p class="mt-3 font-display text-[22px] leading-snug">Ambil nomor antrian lebih dulu agar tidak menunggu lama.</p>
                <a href="{{ route('antrian.form', ['poli' => $layanan->poli_id]) }}"
                   class="tombol mt-6 w-full bg-cream text-ink hover:bg-white !py-3">
                    <x-ikon nama="ticket" kelas="h-[18px] w-[18px]" /> Ambil antrian
                </a>
                <a href="{{ route('jadwal') }}" class="mt-2.5 flex w-full items-center justify-center gap-2 rounded-full border border-white/15 py-3 text-[13.5px] font-semibold text-white/75 transition hover:border-white/35 hover:text-white">
                    <x-ikon nama="calendar" kelas="h-4 w-4" /> Cek jadwal dokter
                </a>
                <p class="mt-5 border-t border-white/10 pt-4 text-[12px] leading-relaxed text-white/40">
                    Nomor online tetap harus diverifikasi di loket dengan identitas asli.
                </p>
            </div>
        </aside>
    </div>
</div>
@endsection
