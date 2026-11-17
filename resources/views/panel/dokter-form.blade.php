@extends('layouts.panel')
@section('judul', $dokter->exists ? 'Ubah tenaga kesehatan' : 'Tenaga kesehatan baru')
@section('remah', 'Konten · Tenaga kesehatan')

@section('konten')
<div class="flex items-center gap-3">
    <a href="{{ route('panel.dokter.index') }}" class="grid h-9 w-9 place-items-center rounded-xl border border-line bg-white text-ink/50 transition hover:text-teal">
        <x-ikon nama="arrow-left" kelas="h-4 w-4" />
    </a>
    <div>
        <h1 class="text-[24px] leading-tight text-ink">{{ $dokter->exists ? 'Ubah tenaga kesehatan' : 'Tenaga kesehatan baru' }}</h1>
        <p class="mt-0.5 text-[13px] text-ink/45">{{ $dokter->exists ? $dokter->nama : 'Lengkapi data dokter atau tenaga kesehatan' }}</p>
    </div>
</div>

<form method="POST"
      action="{{ $dokter->exists ? route('panel.dokter.update', $dokter) : route('panel.dokter.store') }}"
      class="mt-5 grid gap-4 xl:grid-cols-[1.4fr_1fr]">
    @csrf
    @if ($dokter->exists) @method('PUT') @endif

    <div class="kartu space-y-4 p-6">
        <div class="grid gap-4 sm:grid-cols-[1.4fr_1fr]">
            <div>
                <label class="label">Nama lengkap <span class="text-coral">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $dokter->nama) }}" class="input" required maxlength="100"
                       placeholder="Contoh: dr. Siti Rahmawati">
                @error('nama') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Gelar</label>
                <input type="text" name="gelar" value="{{ old('gelar', $dokter->gelar) }}" class="input" maxlength="60"
                       placeholder="Sp.A / M.Ked">
                @error('gelar') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Poli</label>
                <select name="poli_id" class="input">
                    <option value="">Tanpa poli</option>
                    @foreach ($polis as $p)
                        <option value="{{ $p->id }}" @selected(old('poli_id', $dokter->poli_id) == $p->id)>{{ $p->nama }}</option>
                    @endforeach
                </select>
                @error('poli_id') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Nomor SIP</label>
                <input type="text" name="sip" value="{{ old('sip', $dokter->sip) }}" class="input" maxlength="60"
                       placeholder="Nomor izin praktik">
                @error('sip') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label">Profil singkat</label>
            <textarea name="bio" rows="4" class="input resize-none" maxlength="1000"
                      placeholder="Latar belakang, keahlian, atau fokus layanan">{{ old('bio', $dokter->bio) }}</textarea>
            @error('bio') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Urutan tampil</label>
                <input type="number" name="urutan" value="{{ old('urutan', $dokter->urutan ?? 0) }}" min="0" class="input">
            </div>
            <div class="flex items-end">
                <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/65">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $dokter->is_active ?? true))
                           class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                    Aktif praktik
                </label>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Catatan</p>
            <ul class="mt-3 space-y-2.5 text-[12.5px] leading-relaxed text-ink/55">
                <li class="flex gap-2"><span class="text-teal">·</span> Setelah data nakes disimpan, tambahkan jadwal praktik agar muncul di situs.</li>
                <li class="flex gap-2"><span class="text-teal">·</span> Nama yang tidak aktif tetap tampil pada riwayat jadwal lama.</li>
                <li class="flex gap-2"><span class="text-teal">·</span> Nakes yang masih punya jadwal tidak dapat dihapus.</li>
            </ul>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> {{ $dokter->exists ? 'Simpan perubahan' : 'Simpan data' }}
                </button>
                <a href="{{ route('panel.dokter.index') }}" class="tombol tombol-garis w-full">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
