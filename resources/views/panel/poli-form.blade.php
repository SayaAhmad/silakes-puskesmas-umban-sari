@extends('layouts.panel')
@section('judul', $poli->exists ? 'Ubah poli' : 'Poli baru')
@section('remah', 'Konten · Poli')

@section('konten')
@php
    $ikonTersedia = ['stethoscope', 'heart', 'baby', 'tooth', 'eye', 'lab', 'pill', 'shield', 'users', 'leaf'];
    $warnaTersedia = ['#12796a', '#0f5f54', '#e2703f', '#3b7ea1', '#8a6bb1', '#c2543f', '#4a8a5c', '#b8860b'];
    $warnaKini = old('warna', $poli->warna ?: '#12796a');
    $ikonKini = old('ikon', $poli->ikon ?: 'stethoscope');
@endphp

<div class="flex items-center gap-3">
    <a href="{{ route('panel.poli.index') }}" class="grid h-9 w-9 place-items-center rounded-xl border border-line bg-white text-ink/50 transition hover:text-teal">
        <x-ikon nama="arrow-left" kelas="h-4 w-4" />
    </a>
    <div>
        <h1 class="text-[24px] leading-tight text-ink">{{ $poli->exists ? 'Ubah poli' : 'Poli baru' }}</h1>
        <p class="mt-0.5 text-[13px] text-ink/45">{{ $poli->exists ? $poli->nama : 'Tambahkan unit layanan baru' }}</p>
    </div>
</div>

<form method="POST"
      action="{{ $poli->exists ? route('panel.poli.update', $poli) : route('panel.poli.store') }}"
      class="mt-5 grid gap-4 xl:grid-cols-[1.4fr_1fr]"
      x-data="{ warna: @js($warnaKini), ikon: @js($ikonKini), nama: @js(old('nama', $poli->nama)), deskripsi: @js(old('deskripsi', $poli->deskripsi)) }">
    @csrf
    @if ($poli->exists) @method('PUT') @endif

    <div class="kartu space-y-4 p-6">
        <div>
            <label class="label">Nama poli <span class="text-coral">*</span></label>
            <input type="text" name="nama" x-model="nama" value="{{ old('nama', $poli->nama) }}" class="input" required maxlength="80"
                   placeholder="Contoh: Poli Umum">
            @error('nama') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Deskripsi</label>
            <textarea name="deskripsi" rows="4" x-model="deskripsi" class="input resize-none" maxlength="1000"
                      placeholder="Jelaskan singkat layanan poli ini">{{ old('deskripsi', $poli->deskripsi) }}</textarea>
            @error('deskripsi') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Ikon</label>
            <div class="flex flex-wrap gap-2">
                @foreach ($ikonTersedia as $i)
                    <label class="cursor-pointer">
                        <input type="radio" name="ikon" value="{{ $i }}" x-model="ikon" class="peer sr-only">
                        <span class="grid h-11 w-11 place-items-center rounded-xl border border-line bg-white text-ink/40 transition
                                     peer-checked:border-teal peer-checked:bg-teal-soft/60 peer-checked:text-pine hover:border-teal/50">
                            <x-ikon :nama="$i" kelas="h-[18px] w-[18px]" />
                        </span>
                    </label>
                @endforeach
            </div>
            @error('ikon') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Warna</label>
            <input type="hidden" name="warna" x-model="warna">
            <div class="flex flex-wrap items-center gap-2">
                @foreach ($warnaTersedia as $w)
                    <button type="button" @click="warna = '{{ $w }}'"
                            class="h-9 w-9 rounded-xl ring-2 ring-offset-2 transition"
                            :class="warna.toLowerCase() === '{{ $w }}' ? 'ring-ink/40' : 'ring-transparent'"
                            style="background: {{ $w }}" aria-label="Warna {{ $w }}"></button>
                @endforeach
                <input type="text" x-model="warna" maxlength="9"
                       class="input !min-h-0 !w-[116px] !py-1.5 !text-[12px]" placeholder="#12796a">
            </div>
            <p class="mt-1.5 text-[11.5px] text-ink/40">Kode warna HEX dipakai sebagai penanda poli di layar antrian.</p>
            @error('warna') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="label">Urutan tampil</label>
                <input type="number" name="urutan" value="{{ old('urutan', $poli->urutan ?? 0) }}" min="0" class="input">
            </div>
            <div class="flex items-end">
                <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/65">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $poli->is_active ?? true))
                           class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                    Tampilkan di situs publik
                </label>
            </div>
        </div>
    </div>

    {{-- Pratinjau --}}
    <div class="space-y-4">
        <div class="kartu p-6">
            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">Pratinjau kartu</p>

            <div class="mt-4 rounded-2xl border border-line bg-white p-5">
                <span class="grid h-11 w-11 place-items-center rounded-xl text-white" :style="`background:${warna}`">
                    @foreach ($ikonTersedia as $i)
                        <span x-show="ikon === '{{ $i }}'" @if ($i !== $ikonKini) x-cloak @endif>
                            <x-ikon :nama="$i" kelas="h-5 w-5" />
                        </span>
                    @endforeach
                </span>
                <p class="mt-4 text-[15px] font-semibold text-ink" x-text="nama || 'Nama poli'"></p>
                <p class="mt-1 line-clamp-2 text-[12.5px] leading-relaxed text-ink/50" x-text="deskripsi || 'Deskripsi singkat poli akan muncul di sini.'"></p>
            </div>
            <p class="mt-3 text-[11.5px] leading-relaxed text-ink/40">
                Warna dan ikon membantu pasien membedakan poli pada layar panggilan.
            </p>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> {{ $poli->exists ? 'Simpan perubahan' : 'Simpan poli' }}
                </button>
                <a href="{{ route('panel.poli.index') }}" class="tombol tombol-garis w-full">Batal</a>
            </div>
            @if ($poli->exists)
                <p class="mt-3 text-[11.5px] leading-relaxed text-ink/40">
                    Poli dengan riwayat antrian tidak bisa dihapus — nonaktifkan saja agar riwayat tetap utuh.
                </p>
            @endif
        </div>
    </div>
</form>
@endsection
