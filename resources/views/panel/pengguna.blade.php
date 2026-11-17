@extends('layouts.panel')
@section('judul', 'Pengguna')
@section('remah', 'Sistem · Pengguna')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Pengguna panel</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Akun yang dapat masuk ke panel pengelola.</p>
    </div>
    <a href="{{ route('panel.pengguna.create') }}" class="tombol tombol-utama !py-2.5 !text-[13.5px]">
        <x-ikon nama="plus" kelas="h-4 w-4" /> Pengguna baru
    </a>
</div>

<div class="kartu mt-5 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="tabel">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th>Terakhir masuk</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pengguna as $u)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2.5">
                                <span class="grid h-9 w-9 place-items-center rounded-lg bg-teal-soft/60 text-[11.5px] font-bold text-pine">
                                    {{ $u->inisial() }}
                                </span>
                                <div>
                                    <p class="font-medium text-ink">{{ $u->name }}</p>
                                    <p class="text-[11.5px] text-ink/40">{{ $u->jabatan ?: 'Tanpa jabatan' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-ink/60">{{ $u->email }}</td>
                        <td>
                            @if ($u->role === 'admin')
                                <span class="lencana bg-pine/10 text-pine">Admin</span>
                            @else
                                <span class="lencana bg-sand text-ink/60">Petugas</span>
                            @endif
                        </td>
                        <td>
                            @if ($u->is_active)
                                <span class="lencana bg-teal-soft text-pine">Aktif</span>
                            @else
                                <span class="lencana bg-coral-soft text-[#a54d24]">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-[12px] text-ink/45">
                            {{ $u->last_login_at?->translatedFormat('d M Y, H:i') ?? 'Belum pernah' }}
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('panel.pengguna.edit', $u) }}" class="grid h-8 w-8 place-items-center rounded-lg text-ink/35 transition hover:bg-sand hover:text-ink" title="Ubah">
                                    <x-ikon nama="edit" kelas="h-4 w-4" />
                                </a>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('panel.pengguna.destroy', $u) }}" onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-ink/25 transition hover:bg-coral-soft hover:text-coral" title="Hapus">
                                            <x-ikon nama="trash" kelas="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if ($pengguna->hasPages())
    <div class="mt-5">{{ $pengguna->links() }}</div>
@endif
@endsection
