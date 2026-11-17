@extends('layouts.publik')
@section('judul', 'Jadwal Dokter')

@section('konten')
<section class="grain relative overflow-hidden border-b border-line">
    <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-coral-soft/40 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
        <p class="eyebrow">Jadwal praktik</p>
        <h1 class="mt-3 max-w-2xl text-[38px] leading-[1.08] text-ink sm:text-[48px]">
            Ketahui siapa yang praktik<br><span class="italic text-teal">sebelum berangkat.</span>
        </h1>
        <p class="mt-5 max-w-xl text-[15.5px] leading-relaxed text-ink/60">
            Jadwal berlaku untuk hari kerja. Pada hari libur nasional, layanan mengikuti ketentuan puskesmas.
        </p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-5 py-12 sm:px-8">
    {{-- Ringkasan per hari --}}
    <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-7">
        @foreach ($hari as $nomor => $nama)
            @php $jumlah = ($jadwal[$nomor] ?? collect())->count(); @endphp
            <a href="#hari-{{ $nomor }}"
               class="kartu kartu-naik flex flex-col items-center px-3 py-4 text-center {{ $nomor === $hariIni ? '!border-teal !bg-teal-soft/40' : '' }}">
                <span class="text-[11px] font-bold uppercase tracking-[0.1em] {{ $nomor === $hariIni ? 'text-pine' : 'text-ink/40' }}">{{ $nama }}</span>
                <span class="mt-1.5 font-display text-[22px] font-semibold {{ $nomor === $hariIni ? 'text-pine' : 'text-ink' }}">{{ $jumlah }}</span>
                <span class="text-[10.5px] text-ink/35">jadwal</span>
            </a>
        @endforeach
    </div>

    <div class="mt-12 space-y-12">
        @foreach ($hari as $nomor => $nama)
            @php $daftar = $jadwal[$nomor] ?? collect(); @endphp
            @continue($daftar->isEmpty())
            <section id="hari-{{ $nomor }}" class="scroll-mt-28">
                <div class="flex items-center gap-3">
                    <h2 class="text-[24px] leading-none text-ink">{{ $nama }}</h2>
                    @if ($nomor === $hariIni)
                        <span class="lencana bg-teal text-white">Hari ini</span>
                    @endif
                    <span class="h-px flex-1 bg-line"></span>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl border border-line bg-white">
                    @foreach ($daftar as $j)
                        <div class="flex flex-wrap items-center gap-4 border-b border-line/60 px-5 py-4 last:border-0 sm:flex-nowrap">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-ink/[0.04] font-display text-[15px] font-semibold text-ink/70">
                                {{ $j->dokter->inisial() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[14.5px] font-semibold text-ink">{{ $j->dokter->namaLengkap() }}</p>
                                <p class="mt-0.5 text-[12.5px] text-ink/50">
                                    {{ $j->poli->nama ?? 'Umum' }}
                                    @if ($j->dokter->sip) · SIP {{ $j->dokter->sip }} @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-5">
                                <div class="text-right">
                                    <p class="font-display text-[15px] font-semibold text-ink">{{ $j->jam }}</p>
                                    <p class="text-[11px] text-ink/40">kuota {{ $j->kuota }}</p>
                                </div>
                                <a href="{{ route('antrian.form', ['poli' => $j->poli_id]) }}"
                                   class="tombol tombol-garis !min-h-9 !px-3.5 !py-1.5 !text-[12.5px]">Ambil</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <div class="mt-14 flex flex-col items-start gap-4 rounded-2xl border border-line bg-sand/40 p-6 sm:flex-row sm:items-center">
        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-teal">
            <x-ikon nama="bell" kelas="h-5 w-5" />
        </span>
        <p class="flex-1 text-[13.5px] leading-relaxed text-ink/60">
            Jadwal dapat berubah sewaktu-waktu. Untuk kepastian, hubungi puskesmas pada jam kerja
            atau pantau halaman pengumuman.
        </p>
        <a href="{{ route('kontak') }}" class="tombol tombol-garis shrink-0 !min-h-10 !py-2 !text-[13px]">Kontak</a>
    </div>
</div>
@endsection
