@extends('layouts.panel')
@section('judul', 'Log aktivitas')
@section('remah', 'Sistem · Log aktivitas')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Log aktivitas</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Jejak perubahan data dan tindakan pengguna panel.</p>
    </div>
</div>

<form method="GET" class="mt-5 flex flex-wrap items-end gap-3">
    <div class="min-w-[240px] flex-1 max-w-md">
        <label class="label !text-[11px]">Cari</label>
        <input type="text" name="cari" value="{{ $filter['cari'] ?? '' }}" class="input !min-h-0 !py-2 !text-[13px]"
               placeholder="Aksi, subjek, atau deskripsi">
    </div>
    <button type="submit" class="tombol tombol-garis !py-2.5 !text-[13px]">Cari</button>
    @if (!empty($filter['cari']))
        <a href="{{ route('panel.log.index') }}" class="tombol tombol-garis !py-2.5 !text-[13px]">Reset</a>
    @endif
</form>

<div class="kartu mt-4 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th class="w-[150px]">Waktu</th>
                    <th>Pengguna</th>
                    <th>Aksi</th>
                    <th>Subjek</th>
                    <th>Deskripsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($log as $l)
                    <tr>
                        <td class="whitespace-nowrap text-[12px] tabular-nums text-ink/50">
                            {{ $l->created_at?->translatedFormat('d M Y') }}
                            <span class="block text-[11px] text-ink/35">{{ $l->created_at?->format('H:i:s') }}</span>
                        </td>
                        <td>
                            <p class="text-[12.5px] font-medium text-ink">{{ $l->user->name ?? 'Sistem' }}</p>
                            <p class="text-[11px] text-ink/35">{{ $l->user->role ?? '—' }}</p>
                        </td>
                        <td>
                            <code class="rounded-md bg-sand px-2 py-0.5 text-[11px] text-ink/65">{{ $l->aksi }}</code>
                        </td>
                        <td class="text-[12.5px] text-ink/60">{{ $l->subjek ?: '—' }}</td>
                        <td class="max-w-[420px] text-[12.5px] text-ink/55">{{ $l->deskripsi }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-14 text-center text-[13.5px] text-ink/45">Belum ada aktivitas tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($log->hasPages())
    <div class="mt-5">{{ $log->links() }}</div>
@endif
@endsection
