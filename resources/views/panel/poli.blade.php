@extends('layouts.panel')
@section('judul', 'Poli')
@section('remah', 'Konten · Unit layanan')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Poli</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Unit layanan yang tampil di situs publik dan pilihan antrian.</p>
    </div>
    <a href="{{ route('panel.poli.create') }}" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
        <x-ikon nama="plus" kelas="h-4 w-4" /> Poli baru
    </a>
</div>

<div class="kartu mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th class="w-14">Urut</th>
                    <th>Nama poli</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Layanan</th>
                    <th class="text-center">Nakes</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($polis as $p)
                    <tr>
                        <td class="text-ink/40">{{ $p->urutan }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white" style="background: {{ $p->warna }}">
                                    <x-ikon :nama="$p->ikon ?: 'stethoscope'" kelas="h-[17px] w-[17px]" />
                                </span>
                                <div class="min-w-0">
                                    <p class="font-medium text-ink">{{ $p->nama }}</p>
                                    <p class="text-[11.5px] text-ink/35">/{{ $p->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="max-w-[260px]">
                            <p class="truncate text-[12.5px] text-ink/55">{{ $p->deskripsi ?: '—' }}</p>
                        </td>
                        <td class="text-center text-ink/60">{{ $p->layanans_count }}</td>
                        <td class="text-center text-ink/60">{{ $p->dokters_count }}</td>
                        <td>
                            @if ($p->is_active)
                                <span class="lencana bg-teal-soft text-pine">Aktif</span>
                            @else
                                <span class="lencana bg-sand text-ink/45">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('panel.poli.edit', $p) }}" class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Ubah">
                                    <x-ikon nama="edit" kelas="h-4 w-4" />
                                </a>
                                <form method="POST" action="{{ route('panel.poli.destroy', $p) }}" onsubmit="return confirm('Hapus poli {{ $p->nama }}?')">
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
                            <p class="text-[13.5px] text-ink/45">Belum ada poli.</p>
                            <a href="{{ route('panel.poli.create') }}" class="mt-2 inline-block text-[13px] font-semibold text-teal hover:underline">Tambah poli pertama</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($polis->hasPages())
    <div class="mt-5">{{ $polis->links() }}</div>
@endif
@endsection
