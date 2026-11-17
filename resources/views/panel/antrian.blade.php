@extends('layouts.panel')
@section('judul', 'Antrian')
@section('remah', 'Layanan harian')

@section('konten')
@php
    $warnaStatus = [
        'menunggu' => 'bg-sand text-ink/60',
        'dipanggil' => 'bg-coral-soft text-[#a54d24]',
        'selesai' => 'bg-teal-soft text-pine',
        'lewat' => 'bg-sand text-ink/45',
        'batal' => 'bg-coral-soft text-[#a54d24]',
    ];
@endphp

<div class="flex flex-wrap items-start justify-between gap-4">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Antrian</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">
            {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
        </p>
    </div>

    <div class="flex flex-wrap gap-2">
        <form method="POST" action="{{ route('panel.antrian.berikutnya') }}" class="flex gap-2">
            @csrf
            <select name="poli_id" class="input !min-h-0 !w-auto !py-2 !text-[13px]">
                <option value="">Semua poli</option>
                @foreach ($polis as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="tombol tombol-utama !py-2 !text-[13px]">
                <x-ikon nama="bell" kelas="h-4 w-4" /> Panggil berikutnya
            </button>
        </form>
        <a href="{{ route('antrian.layar') }}" target="_blank" class="tombol tombol-garis !py-2 !text-[13px]">
            <x-ikon nama="monitor" kelas="h-4 w-4" /> Layar
        </a>
    </div>
</div>

{{-- Ringkasan --}}
<div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
    @foreach ([
        ['Total', $ringkasan['total'], 'text-ink'],
        ['Menunggu', $ringkasan['menunggu'], 'text-ink'],
        ['Dipanggil', $ringkasan['dipanggil'], 'text-coral'],
        ['Selesai', $ringkasan['selesai'], 'text-teal'],
    ] as $r)
        <div class="kartu px-5 py-4">
            <p class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-ink/40">{{ $r[0] }}</p>
            <p class="mt-1 font-display text-[26px] font-semibold leading-none {{ $r[2] }}">{{ $r[1] }}</p>
        </div>
    @endforeach
</div>

{{-- Filter --}}
<form method="GET" class="kartu mt-4 flex flex-wrap items-end gap-3 p-4">
    <div class="min-w-[150px] flex-1">
        <label class="label !text-[11px]">Tanggal</label>
        <input type="date" name="tanggal" value="{{ $tanggal }}" class="input !min-h-0 !py-2 !text-[13px]">
    </div>
    <div class="min-w-[140px] flex-1">
        <label class="label !text-[11px]">Poli</label>
        <select name="poli" class="input !min-h-0 !py-2 !text-[13px]">
            <option value="">Semua poli</option>
            @foreach ($polis as $p)
                <option value="{{ $p->id }}" @selected(($filter['poli'] ?? '') == $p->id)>{{ $p->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-[140px] flex-1">
        <label class="label !text-[11px]">Status</label>
        <select name="status" class="input !min-h-0 !py-2 !text-[13px]">
            <option value="">Semua status</option>
            @foreach ($status as $k => $v)
                <option value="{{ $k }}" @selected(($filter['status'] ?? '') === $k)>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div class="min-w-[170px] flex-1">
        <label class="label !text-[11px]">Cari</label>
        <input type="text" name="cari" value="{{ $filter['cari'] ?? '' }}" placeholder="Nama atau kode" class="input !min-h-0 !py-2 !text-[13px]">
    </div>
    <button type="submit" class="tombol tombol-utama !py-2.5 !text-[13px]">
        <x-ikon nama="search" kelas="h-4 w-4" /> Terapkan
    </button>
    @if (array_filter($filter))
        <a href="{{ route('panel.antrian.index') }}" class="tombol tombol-garis !py-2.5 !text-[13px]">Reset</a>
    @endif
</form>

{{-- Tabel --}}
<div class="kartu mt-4 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th class="w-16">Kode</th>
                    <th>Pasien</th>
                    <th>Poli</th>
                    <th>Keluhan</th>
                    <th>Sumber</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($antrian as $a)
                    <tr>
                        <td>
                            <span class="inline-grid min-w-[46px] place-items-center rounded-lg bg-sand px-2 py-1 font-display text-[13px] font-semibold text-ink">
                                {{ $a->kode }}
                            </span>
                        </td>
                        <td>
                            <p class="font-medium text-ink">{{ $a->nama_pasien }}</p>
                            <p class="text-[11.5px] text-ink/40">
                                {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                @if ($a->no_hp) · {{ $a->no_hp }} @endif
                            </p>
                        </td>
                        <td class="text-ink/70">{{ $a->poli->nama }}</td>
                        <td class="max-w-[220px]">
                            <p class="truncate text-[12.5px] text-ink/55">{{ $a->keluhan ?: '—' }}</p>
                        </td>
                        <td>
                            <span class="text-[11.5px] font-medium {{ $a->sumber === 'online' ? 'text-teal' : 'text-ink/45' }}">
                                {{ $a->sumber === 'online' ? 'Online' : 'Loket' }}
                            </span>
                        </td>
                        <td>
                            <span class="lencana {{ $warnaStatus[$a->status] ?? 'bg-sand text-ink/55' }}">{{ $a->status_label }}</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                @if (in_array($a->status, ['menunggu', 'lewat']))
                                    <form method="POST" action="{{ route('panel.antrian.panggil', $a) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-line px-2.5 py-1.5 text-[11.5px] font-semibold text-ink/60 transition hover:border-teal hover:text-teal" title="Panggil">
                                            Panggil
                                        </button>
                                    </form>
                                @endif
                                @if (in_array($a->status, ['dipanggil', 'menunggu']))
                                    <form method="POST" action="{{ route('panel.antrian.selesai', $a) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-teal-soft/70 px-2.5 py-1.5 text-[11.5px] font-semibold text-pine transition hover:bg-teal hover:text-white" title="Selesai">
                                            Selesai
                                        </button>
                                    </form>
                                @endif
                                @if ($a->status === 'dipanggil')
                                    <form method="POST" action="{{ route('panel.antrian.lewat', $a) }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-line px-2.5 py-1.5 text-[11.5px] font-semibold text-ink/45 transition hover:border-coral hover:text-coral" title="Tandai terlewat">
                                            Lewat
                                        </button>
                                    </form>
                                @endif
                                @if (in_array($a->status, ['menunggu', 'dipanggil']))
                                    <form method="POST" action="{{ route('panel.antrian.batal', $a) }}" onsubmit="return confirm('Batalkan antrian {{ $a->kode }}?')">
                                        @csrf
                                        <button type="submit" class="grid h-7 w-7 place-items-center rounded-lg text-ink/25 transition hover:bg-coral-soft hover:text-coral" title="Batalkan">
                                            <x-ikon nama="x" kelas="h-3.5 w-3.5" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-14 text-center">
                            <p class="text-[13.5px] text-ink/45">Tidak ada antrian pada filter ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($antrian->hasPages())
    <div class="mt-5">{{ $antrian->links() }}</div>
@endif

{{-- Tambah antrian loket --}}
<div class="kartu mt-6 overflow-hidden" x-data="{ buka: {{ $errors->any() ? 'true' : 'false' }} }">
    <button @click="buka = !buka" class="flex w-full items-center justify-between px-6 py-4 text-left">
        <div>
            <p class="text-[14.5px] font-semibold text-ink">Daftarkan pasien di loket</p>
            <p class="mt-0.5 text-[12px] text-ink/45">Untuk pasien yang datang langsung tanpa antrian online.</p>
        </div>
        <x-ikon nama="plus" kelas="h-5 w-5 shrink-0 text-ink/35 transition" x-bind:class="buka && 'rotate-45'" />
    </button>

    <div x-show="buka" x-cloak x-transition class="border-t border-line px-6 py-5">
        <form method="POST" action="{{ route('panel.antrian.store') }}" class="grid gap-4 sm:grid-cols-2">
            @csrf
            <div>
                <label class="label">Poli <span class="text-coral">*</span></label>
                <select name="poli_id" class="input" required>
                    <option value="">Pilih poli</option>
                    @foreach ($polis as $p)
                        <option value="{{ $p->id }}" @selected(old('poli_id') == $p->id)>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Nama pasien <span class="text-coral">*</span></label>
                <input type="text" name="nama_pasien" value="{{ old('nama_pasien') }}" class="input" required maxlength="100">
            </div>
            <div>
                <label class="label">NIK <span class="font-normal text-ink/35">(opsional)</span></label>
                <input type="text" name="nik" value="{{ old('nik') }}" class="input" maxlength="16" inputmode="numeric">
            </div>
            <div>
                <label class="label">Nomor HP <span class="font-normal text-ink/35">(opsional)</span></label>
                <input type="tel" name="no_hp" value="{{ old('no_hp') }}" class="input" maxlength="20">
            </div>
            <div>
                <label class="label">Jenis kelamin <span class="text-coral">*</span></label>
                <select name="jenis_kelamin" class="input" required>
                    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="label">Keluhan <span class="font-normal text-ink/35">(opsional)</span></label>
                <input type="text" name="keluhan" value="{{ old('keluhan') }}" class="input" maxlength="500">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="tombol tombol-utama">
                    <x-ikon nama="plus" kelas="h-4 w-4" /> Tambahkan ke antrian
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
