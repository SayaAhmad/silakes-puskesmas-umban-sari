@extends('layouts.publik')
@section('judul', 'Alur Layanan')

@section('konten')
<section class="grain relative overflow-hidden border-b border-line">
    <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-teal-soft/40 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
        <p class="eyebrow">Panduan kunjungan</p>
        <h1 class="mt-3 max-w-2xl text-[38px] leading-[1.08] text-ink sm:text-[48px]">
            Dari pintu masuk<br><span class="italic text-teal">sampai pulang.</span>
        </h1>
        <p class="mt-5 max-w-xl text-[15.5px] leading-relaxed text-ink/60">
            Alur ini berlaku untuk pasien umum maupun peserta BPJS. Petugas akan mengarahkan bila ada
            perbedaan prosedur.
        </p>
    </div>
</section>

<div class="mx-auto max-w-5xl px-5 py-14 sm:px-8">
    <div class="relative">
        <div class="absolute left-[31px] top-6 bottom-6 hidden w-px bg-gradient-to-b from-teal/50 via-line to-transparent sm:block"></div>

        <div class="space-y-5">
            @foreach ($langkah as $i => $l)
                <div class="relative flex gap-5">
                    <span class="relative z-10 hidden h-16 w-16 shrink-0 place-items-center rounded-2xl border border-line bg-white text-teal shadow-sm sm:grid">
                        <x-ikon :nama="$l['ikon']" kelas="h-7 w-7" />
                        <span class="absolute -right-2 -top-2 grid h-6 w-6 place-items-center rounded-full bg-ink text-[11px] font-bold text-white">{{ $i + 1 }}</span>
                    </span>

                    <div class="kartu flex-1 p-6">
                        <div class="flex items-start gap-4">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-teal-soft/70 text-pine sm:hidden">
                                <span class="text-[13px] font-bold">{{ $i + 1 }}</span>
                            </span>
                            <div class="min-w-0">
                                <h2 class="text-[19px] font-semibold leading-snug text-ink">{{ $l['judul'] }}</h2>
                                <p class="mt-2 text-[14px] leading-relaxed text-ink/60">{{ $l['isi'] }}</p>
                                @if ($l['butuh'])
                                    <div class="mt-4 inline-flex items-start gap-2 rounded-xl bg-sand/70 px-3.5 py-2.5 text-[12.5px] text-ink/65">
                                        <x-ikon nama="clipboard" kelas="mt-0.5 h-3.5 w-3.5 shrink-0 text-teal" />
                                        <span><span class="font-semibold text-ink/80">Bawa:</span> {{ $l['butuh'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-14 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-teal/25 bg-teal-soft/40 p-6">
            <h3 class="flex items-center gap-2 text-[15.5px] font-semibold text-pine">
                <x-ikon nama="check-bulat" kelas="h-[18px] w-[18px]" /> Tips agar lebih cepat
            </h3>
            <ul class="mt-3 space-y-2 text-[13.5px] leading-relaxed text-pine/80">
                <li>Ambil nomor antrian online sebelum berangkat.</li>
                <li>Siapkan KTP/KK dan kartu BPJS sejak di rumah.</li>
                <li>Datang 15 menit sebelum estimasi jam layanan Anda.</li>
            </ul>
        </div>
        <div class="rounded-2xl border border-line bg-white p-6">
            <h3 class="flex items-center gap-2 text-[15.5px] font-semibold text-ink">
                <x-ikon nama="shield" kelas="h-[18px] w-[18px] text-coral" /> Perlu diketahui
            </h3>
            <p class="mt-3 text-[13.5px] leading-relaxed text-ink/60">
                Website ini menyajikan informasi layanan dan administrasi kunjungan. Pemeriksaan,
                diagnosis, dan tindakan medis hanya dilakukan langsung oleh tenaga kesehatan di puskesmas.
            </p>
        </div>
    </div>

    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('antrian.form') }}" class="tombol tombol-utama">
            <x-ikon nama="ticket" kelas="h-[18px] w-[18px]" /> Ambil nomor antrian
        </a>
        <a href="{{ route('layanan.index') }}" class="tombol tombol-garis">Lihat layanan</a>
    </div>
</div>
@endsection
