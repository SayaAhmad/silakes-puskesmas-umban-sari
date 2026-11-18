@extends('layouts.panel')
@section('judul', 'Pengaturan')
@section('remah', 'Sistem · Pengaturan situs')

@section('konten')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-[26px] leading-tight text-ink">Pengaturan situs</h1>
        <p class="mt-1 text-[13.5px] text-ink/50">Identitas puskesmas dan informasi operasional yang dipakai di seluruh halaman.</p>
    </div>
</div>

<form method="POST" action="{{ route('panel.pengaturan.update') }}" class="mt-5 grid gap-4 xl:grid-cols-[1.45fr_1fr]">
    @csrf
    @method('PUT')

    <div class="space-y-4">
        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Identitas</p>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="label">Nama puskesmas <span class="text-coral">*</span></label>
                    <input type="text" name="nama_puskesmas" value="{{ old('nama_puskesmas', $nilai['nama_puskesmas']) }}" class="input" required maxlength="100">
                    @error('nama_puskesmas') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Alamat</label>
                    <textarea name="alamat" rows="2" class="input resize-none" maxlength="300">{{ old('alamat', $nilai['alamat']) }}</textarea>
                    @error('alamat') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="label">Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $nilai['telepon']) }}" class="input" maxlength="40">
                    </div>
                    <div>
                        <label class="label">WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $nilai['whatsapp']) }}" class="input" maxlength="40">
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $nilai['email']) }}" class="input" maxlength="100">
                        @error('email') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Jam operasional</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="label">Jam buka <span class="text-coral">*</span></label>
                    <input type="time" name="jam_buka" value="{{ old('jam_buka', \Illuminate\Support\Carbon::parse($nilai['jam_buka'])->format('H:i')) }}" class="input" required>
                    @error('jam_buka') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Jam tutup <span class="text-coral">*</span></label>
                    <input type="time" name="jam_tutup" value="{{ old('jam_tutup', \Illuminate\Support\Carbon::parse($nilai['jam_tutup'])->format('H:i')) }}" class="input" required>
                    @error('jam_tutup') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Hari kerja <span class="text-coral">*</span></label>
                    <input type="text" name="hari_kerja" value="{{ old('hari_kerja', $nilai['hari_kerja']) }}" class="input" required maxlength="20"
                           placeholder="Senin–Jumat">
                    @error('hari_kerja') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-4">
                <label class="label">Teks jam operasional</label>
                <input type="text" name="jam_operasional" value="{{ old('jam_operasional', $nilai['jam_operasional']) }}" class="input" maxlength="100"
                       placeholder="Senin–Jumat 08.00–14.00, Sabtu 08.00–11.00">
                <p class="mt-1.5 text-[11.5px] text-ink/40">Kalimat ini tampil apa adanya di beranda dan halaman kontak.</p>
            </div>
        </div>

        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Privasi</p>
            <div class="mt-4">
                <label class="label">Simpan data pribadi (hari) <span class="text-coral">*</span></label>
                <input type="number" name="retensi_hari" value="{{ old('retensi_hari', $nilai['retensi_hari']) }}" class="input" required min="1" max="3650" step="1">
                <p class="mt-1.5 text-[11.5px] text-ink/40">Setelah lewat batas ini, nama, NIK, nomor HP, dan keluhan pasien dikosongkan otomatis. Nomor antrian, status, dan tanggal tetap tersimpan supaya statistik tidak rusak.</p>
                @error('retensi_hari') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Profil & sambutan</p>
            <div class="mt-4 space-y-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Nama kepala puskesmas</label>
                        <input type="text" name="kepala_nama" value="{{ old('kepala_nama', $nilai['kepala_nama']) }}" class="input" maxlength="100">
                    </div>
                    <div>
                        <label class="label">NIP</label>
                        <input type="text" name="kepala_nip" value="{{ old('kepala_nip', $nilai['kepala_nip']) }}" class="input" maxlength="40">
                    </div>
                </div>
                <div>
                    <label class="label">Visi</label>
                    <textarea name="visi" rows="2" class="input resize-none" maxlength="500">{{ old('visi', $nilai['visi']) }}</textarea>
                </div>
                <div>
                    <label class="label">Misi <span class="font-normal text-ink/35">(satu per baris)</span></label>
                    <textarea name="misi" rows="4" class="input resize-none" maxlength="2000">{{ old('misi', $nilai['misi']) }}</textarea>
                </div>
                <div>
                    <label class="label">Sambutan singkat</label>
                    <textarea name="sambutan" rows="4" class="input resize-none" maxlength="1500">{{ old('sambutan', $nilai['sambutan']) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Pratinjau</p>
            <div class="mt-3 rounded-xl bg-cream/70 p-4">
                <p class="font-display text-[19px] leading-tight text-ink">{{ $nilai['nama_puskesmas'] }}</p>
                <p class="mt-1.5 text-[12px] leading-relaxed text-ink/50">{{ $nilai['alamat'] }}</p>
                <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] text-ink/55">
                    <span class="rounded-full bg-white px-2.5 py-1">{{ $nilai['telepon'] }}</span>
                    <span class="rounded-full bg-white px-2.5 py-1">{{ $nilai['email'] }}</span>
                </div>
                <p class="mt-3 border-t border-line pt-3 text-[11.5px] text-ink/45">{{ $nilai['jam_operasional'] }}</p>
            </div>
            <p class="mt-3 text-[11.5px] leading-relaxed text-ink/40">Pratinjau memakai data tersimpan. Simpan perubahan untuk memperbarui.</p>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> Simpan pengaturan
                </button>
                <a href="{{ route('panel.dasbor') }}" class="tombol tombol-garis w-full">Kembali ke dasbor</a>
            </div>
        </div>
    </div>
</form>
@endsection
