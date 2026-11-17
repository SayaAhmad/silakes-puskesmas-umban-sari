@extends('layouts.panel')
@section('judul', 'Layanan')
@section('remah', 'Konten · Jenis layanan')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Layanan</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Daftar layanan beserta persyaratan dan durasi estimasi.</p>
    </div>
    <a href="{{ route('panel.layanan.create') }}" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
        <x-ikon nama="plus" kelas="h-4 w-4" /> Layanan baru
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
        <a href="{{ route('panel.layanan.index') }}" class="tombol tombol-garis !py-2.5 !text-[13px]">Reset</a>
    @endif
</form>

<div class="kartu mt-4 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Nama layanan</th>
                    <th>Poli</th>
                    <th class="text-center">Durasi</th>
                    <th class="text-center">Rujukan</th>
                    <th class="text-center">Syarat</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($layanan as $l)
                    <tr>
                        <td>
                            <p class="font-medium text-ink">{{ $l->nama }}</p>
                            <p class="max-w-[320px] truncate text-[11.5px] text-ink/40">{{ $l->ringkasan ?: $l->slug }}</p>
                        </td>
                        <td>
                            @if ($l->poli)
                                <span class="inline-flex items-center gap-1.5 text-[12.5px] text-ink/70">
                                    <span class="h-2 w-2 rounded-full" style="background: {{ $l->poli->warna }}"></span>
                                    {{ $l->poli->nama }}
                                </span>
                            @else
                                <span class="text-[12.5px] text-ink/30">—</span>
                            @endif
                        </td>
                        <td class="text-center text-ink/60">{{ $l->durasi_menit }} mnt</td>
                        <td class="text-center">
                            @if ($l->butuh_rujukan)
                                <span class="lencana bg-coral-soft text-[#a54d24]">Perlu</span>
                            @else
                                <span class="text-[12px] text-ink/30">Tidak</span>
                            @endif
                        </td>
                        <td class="text-center text-ink/60">{{ count($l->persyaratan ?? []) }}</td>
                        <td>
                            @if ($l->is_active)
                                <span class="lencana bg-teal-soft text-pine">Aktif</span>
                            @else
                                <span class="lencana bg-sand text-ink/45">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('panel.layanan.edit', $l) }}" class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Ubah">
                                    <x-ikon nama="edit" kelas="h-4 w-4" />
                                </a>
                                <form method="POST" action="{{ route('panel.layanan.destroy', $l) }}" onsubmit="return confirm('Hapus layanan {{ $l->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-ink/25 transition hover:bg-coral-soft hover:text-coral" title="Hapus">
                                        <x-ikon nama="trash" kelas="h-4 w-4" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-14 text-center">
                            <p class="text-[13.5px] text-ink/45">Belum ada layanan pada filter ini.</p>
                            <a href="{{ route('panel.layanan.create') }}" class="mt-2 inline-block text-[13px] font-semibold text-teal hover:underline">Tambah layanan</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($layanan->hasPages())
    <div class="mt-5">{{ $layanan->links() }}</div>
@endif
@endsection
