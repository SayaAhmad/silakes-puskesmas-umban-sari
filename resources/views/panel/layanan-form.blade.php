@extends('layouts.panel')
@section('judul', $layanan->exists ? 'Ubah layanan' : 'Layanan baru')
@section('remah', 'Konten · Layanan')

@section('konten')
@php
    $syaratTeks = old('persyaratan', implode("\n", $layanan->persyaratan ?? []));
@endphp

<div class="flex items-center gap-3">
    <a href="{{ route('panel.layanan.index') }}" class="grid h-9 w-9 place-items-center rounded-xl border border-line bg-white text-ink/50 transition hover:text-teal">
        <x-ikon nama="arrow-left" kelas="h-4 w-4" />
    </a>
    <div>
        <h1 class="text-[24px] leading-tight text-ink">{{ $layanan->exists ? 'Ubah layanan' : 'Layanan baru' }}</h1>
        <p class="mt-0.5 text-[13px] text-ink/45">{{ $layanan->exists ? $layanan->nama : 'Tambahkan jenis layanan' }}</p>
    </div>
</div>

<form method="POST"
      action="{{ $layanan->exists ? route('panel.layanan.update', $layanan) : route('panel.layanan.store') }}"
      class="mt-5 grid gap-4 xl:grid-cols-[1.4fr_1fr]">
    @csrf
    @if ($layanan->exists) @method('PUT') @endif

    <div class="kartu space-y-4 p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Nama layanan <span class="text-coral">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $layanan->nama) }}" class="input" required maxlength="100"
                       placeholder="Contoh: Pemeriksaan Umum">
                @error('nama') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Poli</label>
                <select name="poli_id" class="input">
                    <option value="">Tanpa poli</option>
                    @foreach ($polis as $p)
                        <option value="{{ $p->id }}" @selected(old('poli_id', $layanan->poli_id) == $p->id)>{{ $p->nama }}</option>
                    @endforeach
                </select>
                @error('poli_id') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label">Ringkasan singkat</label>
            <input type="text" name="ringkasan" value="{{ old('ringkasan', $layanan->ringkasan) }}" class="input" maxlength="200"
                   placeholder="Satu kalimat yang muncul di daftar layanan">
            @error('ringkasan') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Deskripsi</label>
            <textarea name="deskripsi" rows="5" class="input resize-none" maxlength="3000"
                      placeholder="Penjelasan lengkap layanan, siapa yang dilayani, dan apa yang perlu diketahui pasien">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
            @error('deskripsi') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Persyaratan <span class="font-normal text-ink/35">(satu per baris)</span></label>
            <textarea name="persyaratan" rows="4" class="input resize-none"
                      placeholder="KTP atau Kartu Keluarga&#10;Kartu BPJS/KIS&#10;Surat rujukan">{{ $syaratTeks }}</textarea>
            <p class="mt-1.5 text-[11.5px] text-ink/40">Setiap baris ditampilkan sebagai poin persyaratan di situs publik.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label">Durasi (menit) <span class="text-coral">*</span></label>
                <input type="number" name="durasi_menit" value="{{ old('durasi_menit', $layanan->durasi_menit ?? 15) }}" min="5" max="240" class="input" required>
                @error('durasi_menit') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $layanan->urutan ?? 0) }}" min="0" class="input">
            </div>
            <div class="flex flex-col justify-end gap-2 pb-1">
                <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/65">
                    <input type="checkbox" name="butuh_rujukan" value="1" @checked(old('butuh_rujukan', $layanan->butuh_rujukan))
                           class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                    Butuh surat rujukan
                </label>
                <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/65">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $layanan->is_active ?? true))
                           class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                    Tampilkan di situs
                </label>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Panduan singkat</p>
            <ul class="mt-3 space-y-2.5 text-[12.5px] leading-relaxed text-ink/55">
                <li class="flex gap-2"><span class="text-teal">·</span> Durasi dipakai untuk menghitung estimasi waktu tunggu di tiket antrian.</li>
                <li class="flex gap-2"><span class="text-teal">·</span> Tandai "butuh rujukan" agar pasien tahu harus membawa surat.</li>
                <li class="flex gap-2"><span class="text-teal">·</span> Ringkasan tampil di kartu daftar layanan — buat satu kalimat saja.</li>
                <li class="flex gap-2"><span class="text-teal">·</span> Layanan nonaktif tetap tersimpan tetapi tidak tampil di publik.</li>
            </ul>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> {{ $layanan->exists ? 'Simpan perubahan' : 'Simpan layanan' }}
                </button>
                <a href="{{ route('panel.layanan.index') }}" class="tombol tombol-garis w-full">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
