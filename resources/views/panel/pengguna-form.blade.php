@extends('layouts.panel')
@section('judul', $pengguna->exists ? 'Ubah pengguna' : 'Pengguna baru')
@section('remah', 'Sistem · Pengguna')

@section('konten')
<div class="flex items-center gap-3">
    <a href="{{ route('panel.pengguna.index') }}" class="grid h-9 w-9 place-items-center rounded-xl border border-line bg-white text-ink/50 transition hover:text-teal">
        <x-ikon nama="arrow-left" kelas="h-4 w-4" />
    </a>
    <div>
        <h1 class="text-[24px] leading-tight text-ink">{{ $pengguna->exists ? 'Ubah pengguna' : 'Pengguna baru' }}</h1>
        <p class="mt-0.5 text-[13px] text-ink/45">Akun untuk masuk ke panel pengelola</p>
    </div>
</div>

<form method="POST"
      action="{{ $pengguna->exists ? route('panel.pengguna.update', $pengguna) : route('panel.pengguna.store') }}"
      class="mt-5 grid gap-4 xl:grid-cols-[1.4fr_1fr]">
    @csrf
    @if ($pengguna->exists) @method('PUT') @endif

    <div class="kartu space-y-4 p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Nama lengkap <span class="text-coral">*</span></label>
                <input type="text" name="name" value="{{ old('name', $pengguna->name) }}" class="input" required maxlength="100">
                @error('name') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Jabatan</label>
                <input type="text" name="jabatan" value="{{ old('jabatan', $pengguna->jabatan) }}" class="input" maxlength="80"
                       placeholder="Petugas pendaftaran">
                @error('jabatan') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label">Email <span class="text-coral">*</span></label>
            <input type="email" name="email" value="{{ old('email', $pengguna->email) }}" class="input" required maxlength="100"
                   placeholder="nama@puskesmas.go.id">
            @error('email') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Kata sandi {{ $pengguna->exists ? '' : '*' }}</label>
                <input type="password" name="password" class="input" {{ $pengguna->exists ? '' : 'required' }} autocomplete="new-password"
                       placeholder="{{ $pengguna->exists ? 'Kosongkan bila tidak diubah' : 'Minimal 8 karakter' }}">
                @error('password') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Ulangi kata sandi</label>
                <input type="password" name="password_confirmation" class="input" autocomplete="new-password">
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Peran <span class="text-coral">*</span></label>
                <select name="role" class="input" required>
                    <option value="petugas" @selected(old('role', $pengguna->role) === 'petugas')>Petugas</option>
                    <option value="admin" @selected(old('role', $pengguna->role) === 'admin')>Admin</option>
                </select>
                @error('role') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end pb-1">
                <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/65">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $pengguna->is_active ?? true))
                           class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                    Akun aktif
                </label>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Perbedaan peran</p>
            <div class="mt-3 space-y-3 text-[12.5px] leading-relaxed text-ink/55">
                <div>
                    <p class="font-semibold text-ink/75">Admin</p>
                    <p>Akses penuh, termasuk mengelola pengguna dan melihat log aktivitas.</p>
                </div>
                <div>
                    <p class="font-semibold text-ink/75">Petugas</p>
                    <p>Mengelola antrian, jadwal, dan konten — tanpa akses pengguna dan log.</p>
                </div>
            </div>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> {{ $pengguna->exists ? 'Simpan perubahan' : 'Simpan pengguna' }}
                </button>
                <a href="{{ route('panel.pengguna.index') }}" class="tombol tombol-garis w-full">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
