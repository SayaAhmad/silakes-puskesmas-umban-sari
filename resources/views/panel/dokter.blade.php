@extends('layouts.panel')
@section('judul', 'Tenaga kesehatan')
@section('remah', 'Konten · Tenaga kesehatan')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Tenaga kesehatan</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Dokter dan tenaga kesehatan yang praktik di puskesmas.</p>
    </div>
    <a href="{{ route('panel.dokter.create') }}" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
        <x-ikon nama="plus" kelas="h-4 w-4" /> Tambah
    </a>
</div>

<form method="GET" class="mt-5 flex flex-wrap items-end gap-3">
    <div class="min-w-[180px]">
        <label class="label !text-[11px]">Filter poli</label>
        <select name="poli" class="input !min-h-0 !py-2 !text-[13px]" onchange="this.form.submit()">
            <option value="">Semua poli</option>
            @foreach ($polis as $p)
                <option value="{{ $p->id }}" @selected(($filter['poli'] ?? '') == $p->id)>{{ $p->nama }}</option>
            @endforeach
        </select>
    </div>
    @if (!empty($filter['poli']))
        <a href="{{ route('panel.dokter.index') }}" class="tombol tombol-garis !py-2.5 !text-[13px]">Reset</a>
    @endif
</form>

<div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
    @forelse ($dokter as $d)
        <div class="kartu flex flex-col p-5">
            <div class="flex items-start gap-3">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-teal-soft/70 font-display text-[15px] font-semibold text-pine">
                    {{ $d->inisial() }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[14.5px] font-semibold text-ink">{{ $d->nama }}</p>
                    <p class="truncate text-[12px] text-ink/45">{{ $d->gelar ?: 'Tenaga kesehatan' }}</p>
                </div>
                @unless ($d->is_active)
                    <span class="lencana bg-sand text-ink/45">Nonaktif</span>
                @endunless
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                @if ($d->poli)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sand px-2.5 py-1 text-[11.5px] font-medium text-ink/65">
                        <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $d->poli->warna }}"></span>
                        {{ $d->poli->nama }}
                    </span>
                @endif
                @if ($d->sip)
                    <span class="rounded-full bg-sand px-2.5 py-1 text-[11px] text-ink/45">SIP {{ $d->sip }}</span>
                @endif
            </div>

            @if ($d->bio)
                <p class="mt-3 line-clamp-2 flex-1 text-[12.5px] leading-relaxed text-ink/50">{{ $d->bio }}</p>
            @else
                <div class="flex-1"></div>
            @endif

            <div class="mt-4 flex items-center justify-between border-t border-line/70 pt-3">
                <span class="text-[11.5px] text-ink/40">{{ $d->jadwals_count ?? $d->jadwals()->count() }} jadwal</span>
                <div class="flex items-center gap-1">
                    <a href="{{ route('panel.dokter.edit', $d) }}" class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Ubah">
                        <x-ikon nama="edit" kelas="h-4 w-4" />
                    </a>
                    <form method="POST" action="{{ route('panel.dokter.destroy', $d) }}" onsubmit="return confirm('Hapus {{ $d->nama }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-ink/25 transition hover:bg-coral-soft hover:text-coral" title="Hapus">
                            <x-ikon nama="trash" kelas="h-4 w-4" />
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full rounded-2xl border border-dashed border-line p-12 text-center">
            <p class="text-[13.5px] text-ink/45">Belum ada tenaga kesehatan terdaftar.</p>
            <a href="{{ route('panel.dokter.create') }}" class="mt-2 inline-block text-[13px] font-semibold text-teal hover:underline">Tambah sekarang</a>
        </div>
    @endforelse
</div>
@endsection
