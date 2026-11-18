@extends('layouts.panel')
@section('judul', 'Dasbor')
@section('remah', 'Ringkasan hari ini')

@section('konten')
@php
    $maks = max(1, $hariTren->max('jumlah'));
    $labelStatus = ['menunggu' => 'Menunggu', 'dipanggil' => 'Dipanggil', 'selesai' => 'Selesai', 'lewat' => 'Lewat', 'batal' => 'Batal'];
    $warnaStatus = ['menunggu' => 'bg-sand text-ink/60', 'dipanggil' => 'bg-coral-soft text-[#a54d24]', 'selesai' => 'bg-teal-soft text-pine', 'lewat' => 'bg-sand text-ink/45', 'batal' => 'bg-coral-soft text-[#a54d24]'];
@endphp

{{-- Sambutan --}}
<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-[26px] leading-tight text-ink sm:text-[30px]">
            Selamat {{ now()->hour < 11 ? 'pagi' : (now()->hour < 15 ? 'siang' : (now()->hour < 18 ? 'sore' : 'malam')) }},
            <span class="italic text-teal">{{ explode(' ', auth()->user()->name)[0] }}</span>
        </h1>
        <p class="mt-1.5 text-[13.5px] text-ink/50">{{ now()->translatedFormat('l, d F Y') }} · Puskesmas Umban Sari</p>
    </div>
    <div class="flex gap-2">
        <form method="POST" action="{{ route('panel.antrian.berikutnya') }}">
            @csrf
            <button type="submit" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
                <x-ikon nama="bell" kelas="h-4 w-4" /> Panggil berikutnya
            </button>
        </form>
        <a href="{{ route('panel.antrian.index') }}" class="tombol tombol-garis !py-2.5 !text-[13.5px]">Kelola antrian</a>
    </div>
</div>

{{-- Kartu ringkasan --}}
<div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([
        ['Total hari ini', $ringkasan['antrian_hari_ini'], 'ticket', 'bg-ink text-white', 'text-white/45'],
        ['Menunggu', $ringkasan['menunggu'], 'hourglass', 'kartu', 'text-ink/45'],
        ['Sedang dipanggil', $ringkasan['dipanggil'], 'bell', 'kartu', 'text-ink/45'],
        ['Selesai dilayani', $ringkasan['selesai'], 'check-bulat', 'kartu', 'text-ink/45'],
    ] as $k)
        <div class="{{ $k[3] }} relative overflow-hidden p-5">
            @if ($k[3] !== 'kartu')
                <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-teal/25 blur-2xl"></div>
            @endif
            <div class="relative flex items-start justify-between">
                <p class="text-[11px] font-bold uppercase tracking-[0.12em] {{ $k[4] }}">{{ $k[0] }}</p>
                <x-ikon :nama="$k[2]" kelas="h-4 w-4 {{ $k[3] === 'kartu' ? 'text-ink/20' : 'text-white/25' }}" />
            </div>
            <p class="relative mt-3 font-display text-[34px] font-semibold leading-none">{{ $k[1] }}</p>
            @if ($k[0] === 'Selesai dilayani' && $rataTunggu)
                <p class="relative mt-2 text-[11.5px] {{ $k[4] }}">Rata-rata tunggu {{ $rataTunggu }} menit · 30 hari terakhir</p>
            @endif
        </div>
    @endforeach
</div>

<div class="mt-4 grid gap-4 xl:grid-cols-[1.35fr_1fr]">

    {{-- Tren --}}
    <div class="kartu p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-[15.5px] font-semibold text-ink">Kunjungan 7 hari terakhir</h2>
                <p class="mt-0.5 text-[12px] text-ink/45">Jumlah antrian yang terdaftar per hari</p>
            </div>
            <span class="lencana bg-sand text-ink/55">{{ $hariTren->sum('jumlah') }} total</span>
        </div>

        <div class="mt-7 flex h-[168px] items-end gap-2.5">
            @foreach ($hariTren as $h)
                @php $tinggi = (int) round(($h['jumlah'] / $maks) * 100); @endphp
                <div class="group flex flex-1 flex-col items-center justify-end gap-2">
                    <span class="text-[11px] font-semibold text-ink/40 opacity-0 transition group-hover:opacity-100">{{ $h['jumlah'] }}</span>
                    <div class="w-full rounded-t-lg transition-all duration-500 {{ $h['jumlah'] > 0 ? 'bg-teal/80 group-hover:bg-teal' : 'bg-sand' }}"
                         style="height: {{ max($tinggi, 4) }}%"></div>
                    <div class="text-center">
                        <p class="text-[10.5px] font-semibold uppercase tracking-wide text-ink/45">{{ $h['label'] }}</p>
                        <p class="text-[10px] text-ink/30">{{ $h['tanggal'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Per poli --}}
    <div class="kartu p-6">
        <h2 class="text-[15.5px] font-semibold text-ink">Antrian per poli</h2>
        <p class="mt-0.5 text-[12px] text-ink/45">Hari ini</p>

        <div class="mt-5 space-y-3.5">
            @forelse ($perPoli as $p)
                @php $persen = $ringkasan['antrian_hari_ini'] > 0 ? round(($p['jumlah'] / $ringkasan['antrian_hari_ini']) * 100) : 0; @endphp
                <div>
                    <div class="flex items-center justify-between text-[13px]">
                        <span class="flex items-center gap-2 font-medium text-ink">
                            <span class="h-2 w-2 rounded-full" style="background: {{ $p['warna'] }}"></span>
                            {{ $p['nama'] }}
                        </span>
                        <span class="text-ink/50">
                            {{ $p['jumlah'] }} <span class="text-ink/30">· {{ $p['menunggu'] }} tunggu</span>
                        </span>
                    </div>
                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-sand">
                        <div class="h-full rounded-full transition-all duration-700" style="width: {{ $persen }}%; background: {{ $p['warna'] }}"></div>
                    </div>
                </div>
            @empty
                <p class="py-6 text-center text-[13px] text-ink/40">Belum ada poli aktif.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="mt-4 grid gap-4 xl:grid-cols-2">

    {{-- Antrian aktif --}}
    <div class="kartu overflow-hidden">
        <div class="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 class="text-[15.5px] font-semibold text-ink">Antrian berjalan</h2>
            <a href="{{ route('panel.antrian.index') }}" class="flex items-center gap-1 text-[12.5px] font-semibold text-teal hover:underline">
                Semua <x-ikon nama="arrow-right" kelas="h-3.5 w-3.5" />
            </a>
        </div>

        @if ($antrianAktif->isEmpty())
            <p class="px-6 py-10 text-center text-[13px] text-ink/40">Belum ada antrian yang menunggu.</p>
        @else
            <div class="divide-y divide-line/60">
                @foreach ($antrianAktif as $a)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <span class="grid h-10 w-11 shrink-0 place-items-center rounded-lg bg-sand font-display text-[14px] font-semibold text-ink">
                            {{ $a->kode }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[13.5px] font-medium text-ink">{{ $a->nama_pasien }}</p>
                            <p class="truncate text-[11.5px] text-ink/45">{{ $a->poli->nama }}</p>
                        </div>
                        <span class="lencana {{ $warnaStatus[$a->status] ?? 'bg-sand text-ink/55' }}">{{ $labelStatus[$a->status] ?? $a->status }}</span>
                        <form method="POST" action="{{ route('panel.antrian.panggil', $a) }}">
                            @csrf
                            <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg border border-line text-ink/40 transition hover:border-teal hover:text-teal" title="Panggil">
                                <x-ikon nama="bell" kelas="h-3.5 w-3.5" />
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Jadwal + log --}}
    <div class="space-y-4">
        <div class="kartu overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-6 py-4">
                <h2 class="text-[15.5px] font-semibold text-ink">Jadwal praktik hari ini</h2>
                <span class="lencana bg-teal-soft/70 text-pine">{{ $jadwalHariIni->count() }} sesi</span>
            </div>
            @if ($jadwalHariIni->isEmpty())
                <p class="px-6 py-8 text-center text-[13px] text-ink/40">Tidak ada jadwal praktik hari ini.</p>
            @else
                <div class="divide-y divide-line/60">
                    @foreach ($jadwalHariIni as $j)
                        <div class="flex items-center gap-3 px-5 py-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-teal-soft/60 text-[11px] font-bold text-pine">
                                {{ $j->dokter->inisial() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13px] font-medium text-ink">{{ $j->dokter->nama }}</p>
                                <p class="truncate text-[11.5px] text-ink/45">{{ $j->poli->nama ?? 'Umum' }}</p>
                            </div>
                            <span class="text-[12px] font-semibold tabular-nums text-ink/60">{{ $j->jam }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="kartu overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-6 py-4">
                <h2 class="text-[15.5px] font-semibold text-ink">Aktivitas terbaru</h2>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('panel.log.index') }}" class="text-[12.5px] font-semibold text-teal hover:underline">Log</a>
                @endif
            </div>
            @if ($logTerbaru->isEmpty())
                <p class="px-6 py-8 text-center text-[13px] text-ink/40">Belum ada aktivitas tercatat.</p>
            @else
                <div class="divide-y divide-line/60">
                    @foreach ($logTerbaru as $l)
                        <div class="flex items-start gap-3 px-5 py-3">
                            <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-teal/60"></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-[12.5px] leading-snug text-ink/75">{{ $l->deskripsi ?? $l->aksi }}</p>
                                <p class="mt-0.5 text-[11px] text-ink/35">{{ $l->user->name ?? 'Sistem' }} · {{ $l->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Pintasan --}}
<div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ([
        ['panel.poli.index', 'layers', 'Poli', $jumlah['poli'] . ' unit'],
        ['panel.layanan.index', 'stethoscope', 'Layanan', $jumlah['layanan'] . ' jenis'],
        ['panel.dokter.index', 'users', 'Tenaga kesehatan', $jumlah['dokter'] . ' orang'],
        ['panel.pengumuman.index', 'megaphone', 'Pengumuman', $jumlah['pengumuman'] . ' entri'],
    ] as $p)
        <a href="{{ route($p[0]) }}" class="kartu kartu-naik group flex items-center gap-3.5 p-4">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-sand text-ink/50 transition group-hover:bg-teal group-hover:text-white">
                <x-ikon :nama="$p[1]" kelas="h-[18px] w-[18px]" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-[13.5px] font-semibold text-ink">{{ $p[2] }}</p>
                <p class="text-[11.5px] text-ink/40">{{ $p[3] }}</p>
            </div>
            <x-ikon nama="arrow-right" kelas="h-4 w-4 shrink-0 text-ink/20 transition group-hover:translate-x-0.5 group-hover:text-teal" />
        </a>
    @endforeach
</div>
@endsection
