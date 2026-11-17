@extends('layouts.panel')
@section('judul', 'Jadwal praktik')
@section('remah', 'Konten · Jadwal praktik')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Jadwal praktik</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Jadwal ini yang tampil di halaman jadwal publik dan menentukan poli yang bisa dipilih saat ambil antrian.</p>
    </div>
    <a href="{{ route('panel.jadwal.create') }}" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
        <x-ikon nama="plus" kelas="h-4 w-4" /> Tambah jadwal
    </a>
</div>

<div class="mt-5 space-y-3">
    @foreach ($hari as $nomor => $nama)
        @php $daftar = $jadwal->get($nomor) ?? collect(); @endphp
        <div class="kartu overflow-hidden {{ $nomor === now()->dayOfWeek ? 'ring-1 ring-teal/30' : '' }}">
            <div class="flex items-center justify-between border-b border-line px-5 py-3.5">
                <div class="flex items-center gap-2.5">
                    <h2 class="text-[14.5px] font-semibold text-ink">{{ $nama }}</h2>
                    @if ($nomor === now()->dayOfWeek)
                        <span class="lencana bg-teal-soft text-pine">Hari ini</span>
                    @endif
                </div>
                <span class="text-[12px] text-ink/40">{{ $daftar->count() }} sesi</span>
            </div>

            @if ($daftar->isEmpty())
                <p class="px-5 py-5 text-[12.5px] text-ink/35">Tidak ada jadwal praktik.</p>
            @else
                <div class="divide-y divide-line/60">
                    @foreach ($daftar as $j)
                        <div class="flex flex-wrap items-center gap-3 px-5 py-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-teal-soft/60 text-[11px] font-bold text-pine">
                                {{ $j->dokter->inisial() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-[13.5px] font-medium text-ink">
                                    {{ $j->dokter->nama }}
                                    @if ($j->dokter->gelar)
                                        <span class="font-normal text-ink/40">, {{ $j->dokter->gelar }}</span>
                                    @endif
                                </p>
                                <p class="truncate text-[11.5px] text-ink/45">
                                    {{ $j->poli->nama ?? 'Umum' }}
                                    @unless ($j->is_active)
                                        · <span class="text-coral">nonaktif</span>
                                    @endunless
                                </p>
                            </div>
                            <span class="rounded-lg bg-sand px-2.5 py-1 text-[12px] font-semibold tabular-nums text-ink/70">{{ $j->jam }}</span>
                            <span class="text-[11.5px] text-ink/40">kuota {{ $j->kuota }}</span>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('panel.jadwal.edit', $j) }}" class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Ubah">
                                    <x-ikon nama="edit" kelas="h-4 w-4" />
                                </a>
                                <form method="POST" action="{{ route('panel.jadwal.destroy', $j) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-ink/25 transition hover:bg-coral-soft hover:text-coral" title="Hapus">
                                        <x-ikon nama="trash" kelas="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
