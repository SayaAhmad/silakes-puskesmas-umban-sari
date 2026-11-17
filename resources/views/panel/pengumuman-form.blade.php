@extends('layouts.panel')
@section('judul', $pengumuman->exists ? 'Ubah pengumuman' : 'Pengumuman baru')
@section('remah', 'Konten · Pengumuman')

@section('konten')
<div class="flex items-center gap-3">
    <a href="{{ route('panel.pengumuman.index') }}" class="grid h-9 w-9 place-items-center rounded-xl border border-line bg-white text-ink/50 transition hover:text-teal">
        <x-ikon nama="arrow-left" kelas="h-4 w-4" />
    </a>
    <div>
        <h1 class="text-[24px] leading-tight text-ink">{{ $pengumuman->exists ? 'Ubah pengumuman' : 'Pengumuman baru' }}</h1>
        <p class="mt-0.5 text-[13px] text-ink/45">Tulis informasi untuk pasien dan masyarakat</p>
    </div>
</div>

<form method="POST"
      action="{{ $pengumuman->exists ? route('panel.pengumuman.update', $pengumuman) : route('panel.pengumuman.store') }}"
      class="mt-5 grid gap-4 xl:grid-cols-[1.45fr_1fr]">
    @csrf
    @if ($pengumuman->exists) @method('PUT') @endif

    <div class="kartu space-y-4 p-6">
        <div>
            <label class="label">Judul <span class="text-coral">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $pengumuman->judul) }}" class="input" required maxlength="150"
                   placeholder="Contoh: Perubahan jam layanan selama libur nasional">
            @error('judul') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Ringkasan</label>
            <textarea name="ringkasan" rows="2" class="input resize-none" maxlength="300"
                      placeholder="Satu atau dua kalimat yang tampil di kartu pengumuman">{{ old('ringkasan', $pengumuman->ringkasan) }}</textarea>
            @error('ringkasan') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Isi pengumuman <span class="text-coral">*</span></label>
            <textarea name="isi" rows="12" class="input resize-none" required
                      placeholder="Tulis isi lengkap. Pisahkan paragraf dengan satu baris kosong.">{{ old('isi', $pengumuman->isi) }}</textarea>
            <p class="mt-1.5 text-[11.5px] text-ink/40">Baris kosong dipisah menjadi paragraf di halaman publik.</p>
            @error('isi') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="space-y-4">
        <div class="kartu space-y-4 p-6">
            <div>
                <label class="label">Kategori <span class="text-coral">*</span></label>
                <select name="kategori" class="input" required>
                    @foreach (\App\Models\Pengumuman::KATEGORI as $k => $v)
                        <option value="{{ $k }}" @selected(old('kategori', $pengumuman->kategori) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                @error('kategori') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Tanggal terbit</label>
                <input type="date" name="published_at"
                       value="{{ old('published_at', $pengumuman->published_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                       class="input">
                <p class="mt-1.5 text-[11.5px] text-ink/40">Kosongkan untuk memakai waktu sekarang.</p>
                @error('published_at') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>

            <label class="flex cursor-pointer items-start gap-2.5 text-[13px] text-ink/65">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $pengumuman->is_published ?? true))
                       class="mt-0.5 h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                <span>Terbitkan di situs publik<br><span class="text-[11.5px] text-ink/40">Jika tidak dicentang, pengumuman disimpan sebagai draf.</span></span>
            </label>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> {{ $pengumuman->exists ? 'Simpan perubahan' : 'Simpan pengumuman' }}
                </button>
                <a href="{{ route('panel.pengumuman.index') }}" class="tombol tombol-garis w-full">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
