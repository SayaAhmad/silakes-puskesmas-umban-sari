@extends('layouts.panel')
@section('judul', $faq->exists ? 'Ubah FAQ' : 'FAQ baru')
@section('remah', 'Konten · FAQ')

@section('konten')
<div class="flex items-center gap-3">
    <a href="{{ route('panel.faq.index') }}" class="grid h-9 w-9 place-items-center rounded-xl border border-line bg-white text-ink/50 transition hover:text-teal">
        <x-ikon nama="arrow-left" kelas="h-4 w-4" />
    </a>
    <div>
        <h1 class="text-[24px] leading-tight text-ink">{{ $faq->exists ? 'Ubah FAQ' : 'FAQ baru' }}</h1>
        <p class="mt-0.5 text-[13px] text-ink/45">Pertanyaan dan jawaban singkat</p>
    </div>
</div>

<form method="POST"
      action="{{ $faq->exists ? route('panel.faq.update', $faq) : route('panel.faq.store') }}"
      class="mt-5 grid gap-4 xl:grid-cols-[1.4fr_1fr]">
    @csrf
    @if ($faq->exists) @method('PUT') @endif

    <div class="kartu space-y-4 p-6">
        <div>
            <label class="label">Pertanyaan <span class="text-coral">*</span></label>
            <input type="text" name="pertanyaan" value="{{ old('pertanyaan', $faq->pertanyaan) }}" class="input" required maxlength="200"
                   placeholder="Contoh: Apakah bisa berobat tanpa kartu BPJS?">
            @error('pertanyaan') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Jawaban <span class="text-coral">*</span></label>
            <textarea name="jawaban" rows="7" class="input resize-none" required maxlength="2000"
                      placeholder="Jawab dengan singkat, jelas, dan sesuai prosedur puskesmas.">{{ old('jawaban', $faq->jawaban) }}</textarea>
            @error('jawaban') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="space-y-4">
        <div class="kartu space-y-4 p-6">
            <div>
                <label class="label">Kategori <span class="text-coral">*</span></label>
                <select name="kategori" class="input" required>
                    @foreach (\App\Models\Faq::KATEGORI as $k => $v)
                        <option value="{{ $k }}" @selected(old('kategori', $faq->kategori) === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                @error('kategori') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $faq->urutan ?? 0) }}" min="0" class="input">
            </div>

            <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/65">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active ?? true))
                       class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                Tampilkan di situs publik
            </label>
        </div>

        <div class="kartu p-6">
            <div class="flex flex-col gap-2">
                <button type="submit" class="tombol tombol-utama w-full">
                    <x-ikon nama="check" kelas="h-4 w-4" /> {{ $faq->exists ? 'Simpan perubahan' : 'Simpan FAQ' }}
                </button>
                <a href="{{ route('panel.faq.index') }}" class="tombol tombol-garis w-full">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
