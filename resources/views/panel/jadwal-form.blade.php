@extends('layouts.panel')
@section('judul', $jadwal->exists ? 'Ubah jadwal' : 'Jadwal baru')
@section('remah', 'Konten · Jadwal praktik')

@section('konten')
<div class="flex items-center gap-3">
    <a href="{{ route('panel.jadwal.index') }}" class="grid h-9 w-9 place-items-center rounded-xl border border-line bg-white text-ink/50 transition hover:text-teal">
        <x-ikon nama="arrow-left" kelas="h-4 w-4" />
    </a>
    <div>
        <h1 class="text-[24px] leading-tight text-ink">{{ $jadwal->exists ? 'Ubah jadwal' : 'Jadwal baru' }}</h1>
        <p class="mt-0.5 text-[13px] text-ink/45">Atur hari, jam, dan kuota praktik</p>
    </div>
</div>

<form method="POST"
      action="{{ $jadwal->exists ? route('panel.jadwal.update', $jadwal) : route('panel.jadwal.store') }}"
      class="mt-5 grid gap-4 xl:grid-cols-[1.4fr_1fr]">
    @csrf
    @if ($jadwal->exists) @method('PUT') @endif

    <div class="kartu space-y-4 p-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Tenaga kesehatan <span class="text-coral">*</span></label>
                <select name="dokter_id" class="input" required>
                    <option value="">Pilih nakes</option>
                    @foreach ($dokter as $d)
                        <option value="{{ $d->id }}" @selected(old('dokter_id', $jadwal->dokter_id) == $d->id)>
                            {{ $d->nama }}{{ $d->gelar ? ', ' . $d->gelar : '' }}
                        </option>
                    @endforeach
                </select>
                @error('dokter_id') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Poli</label>
                <select name="poli_id" class="input">
                    <option value="">Ikuti poli nakes</option>
                    @foreach ($polis as $p)
                        <option value="{{ $p->id }}" @selected(old('poli_id', $jadwal->poli_id) == $p->id)>{{ $p->nama }}</option>
                    @endforeach
                </select>
                @error('poli_id') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label">Hari <span class="text-coral">*</span></label>
            <div class="flex flex-wrap gap-2">
                @foreach ($hari as $nomor => $nama)
                    <label class="cursor-pointer">
                        <input type="radio" name="hari" value="{{ $nomor }}" class="peer sr-only" required
                               @checked(old('hari', $jadwal->hari ?? now()->dayOfWeek) == $nomor)>
                        <span class="inline-block rounded-xl border border-line bg-white px-3.5 py-2 text-[12.5px] font-medium text-ink/60 transition
                                     peer-checked:border-teal peer-checked:bg-teal-soft/50 peer-checked:text-pine hover:border-teal/50">
                            {{ $nama }}
                        </span>
                    </label>
                @endforeach
            </div>
            @error('hari') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="label">Jam mulai <span class="text-coral">*</span></label>
                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai ? \Illuminate\Support\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '08:00') }}"
                       class="input" required>
                @error('jam_mulai') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Jam selesai <span class="text-coral">*</span></label>
                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai ? \Illuminate\Support\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '14:00') }}"
                       class="input" required>
                @error('jam_selesai') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="label">Kuota pasien <span class="text-coral">*</span></label>
                <input type="number" name="kuota" value="{{ old('kuota', $jadwal->kuota ?? 30) }}" min="1" max="300" class="input" required>
                @error('kuota') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>
        </div>

        <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/65">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $jadwal->is_active ?? true))
                   class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
            Jadwal aktif dan tampil di situs publik
        </label>
    </div>

    <div class="space-y-4">
        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Hal yang perlu diingat</p>
            <ul class="mt-3 space-y-2.5 text-[12.5px] leading-relaxed text-ink/55">
                <li class="flex gap-2"><span class="text-teal">·</span> Sistem menolak jadwal yang bertabrakan untuk nakes yang sama pada hari dan jam yang sama.</li>
                <li class="flex gap-2"><span class="text-teal">·</span> Kuota membatasi jumlah antrian yang bisa diambil per sesi.</li>
                <li class="flex gap-2"><span class="text-teal">·</span> Antrian online hanya bisa dibuat pada hari yang memiliki jadwal aktif.</li>
            </ul>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> {{ $jadwal->exists ? 'Simpan perubahan' : 'Simpan jadwal' }}
                </button>
                <a href="{{ route('panel.jadwal.index') }}" class="tombol tombol-garis w-full">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
