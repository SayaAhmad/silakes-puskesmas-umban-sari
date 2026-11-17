@extends('layouts.publik')
@section('judul', 'Ambil Nomor Antrian')

@section('konten')
<div class="mx-auto max-w-7xl px-5 py-10 sm:px-8">
    <nav class="flex items-center gap-2 text-[12.5px] text-ink/45">
        <a href="{{ route('beranda') }}" class="hover:text-teal">Beranda</a>
        <span>/</span>
        <span class="text-ink/70">Antrian online</span>
    </nav>

    <div class="mt-7 grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:gap-14">

        {{-- Formulir --}}
        <div>
            <p class="eyebrow">Antrian online</p>
            <h1 class="mt-3 text-[36px] leading-[1.1] text-ink sm:text-[42px]">
                Ambil nomor,<br><span class="italic text-teal">lalu datang tepat waktu.</span>
            </h1>
            <p class="mt-4 max-w-lg text-[14.5px] leading-relaxed text-ink/60">
                Nomor hanya berlaku untuk hari ini. Setelah terdaftar, simpan kode tiket dan tunjukkan
                ke petugas loket untuk verifikasi.
            </p>

            @if ($errors->any())
                <div class="mt-7 rounded-2xl border border-coral/30 bg-coral-soft/50 px-5 py-4">
                    <p class="flex items-center gap-2 text-[13px] font-semibold text-[#a54d24]">
                        <x-ikon nama="bell" kelas="h-4 w-4" /> Periksa kembali data Anda
                    </p>
                    <ul class="mt-2 space-y-1 text-[13px] text-[#8f451f]">
                        @foreach ($errors->all() as $e)
                            <li>· {{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('antrian.simpan') }}" class="mt-8 space-y-5" x-data="{ poli: '{{ old('poli_id', $pilihan) }}' }">
                @csrf

                {{-- Pilih poli --}}
                <div>
                    <label class="label">Poli tujuan <span class="text-coral">*</span></label>
                    <div class="grid gap-2.5 sm:grid-cols-2">
                        @foreach ($polis as $p)
                            @php
                                $jumlah = $antrianHariIni[$p->id] ?? 0;
                                $buka = in_array($p->id, $poliBukaHariIni, true);
                            @endphp
                            <label class="relative {{ $buka ? 'cursor-pointer' : 'cursor-not-allowed opacity-55' }}">
                                <input type="radio" name="poli_id" value="{{ $p->id }}" x-model="poli" class="peer sr-only" required {{ $buka ? '' : 'disabled' }}>
                                <div class="flex items-center gap-3 rounded-xl border border-line bg-white px-4 py-3 transition
                                            peer-checked:border-teal peer-checked:bg-teal-soft/30 peer-checked:ring-2 peer-checked:ring-teal/15
                                            {{ $buka ? 'hover:border-teal/50' : '' }}">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg text-white" style="background: {{ $p->warna }}">
                                        <x-ikon :nama="$p->ikon ?: 'stethoscope'" kelas="h-[18px] w-[18px]" />
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate text-[13.5px] font-semibold text-ink">{{ $p->nama }}</span>
                                        <span class="block text-[11.5px] text-ink/45">
                                            @if ($buka)
                                                {{ $jumlah }} antrian hari ini
                                            @else
                                                Tidak praktik hari ini
                                            @endif
                                        </span>
                                    </span>
                                    <x-ikon nama="check-bulat" kelas="h-4 w-4 shrink-0 text-teal opacity-0 transition peer-checked:opacity-100" />
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('poli_id') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="nama_pasien" class="label">Nama lengkap pasien <span class="text-coral">*</span></label>
                        <input id="nama_pasien" name="nama_pasien" type="text" value="{{ old('nama_pasien') }}"
                               class="input" placeholder="Sesuai KTP / kartu berobat" required maxlength="100">
                        @error('nama_pasien') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nik" class="label">NIK <span class="font-normal text-ink/35">(opsional)</span></label>
                        <input id="nik" name="nik" type="text" inputmode="numeric" value="{{ old('nik') }}"
                               class="input" placeholder="16 digit" maxlength="16">
                        @error('nik') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="no_hp" class="label">Nomor HP <span class="font-normal text-ink/35">(opsional)</span></label>
                        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}"
                               class="input" placeholder="08xxxxxxxxxx" maxlength="20">
                        @error('no_hp') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="label">Jenis kelamin <span class="text-coral">*</span></label>
                        <div class="flex gap-2.5">
                            @foreach (['L' => 'Laki-laki', 'P' => 'Perempuan'] as $nilai => $teks)
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="jenis_kelamin" value="{{ $nilai }}" class="peer sr-only"
                                        {{ old('jenis_kelamin') === $nilai ? 'checked' : '' }} required>
                                    <div class="rounded-xl border border-line bg-white py-3 text-center text-[13.5px] font-semibold text-ink/60 transition
                                                peer-checked:border-teal peer-checked:bg-teal-soft/30 peer-checked:text-pine">
                                        {{ $teks }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('jenis_kelamin') <p class="mt-1.5 text-[12.5px] text-coral">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="keluhan" class="label">Keluhan singkat <span class="font-normal text-ink/35">(opsional)</span></label>
                        <textarea id="keluhan" name="keluhan" rows="3" class="input resize-none"
                                  placeholder="Misalnya: demam tiga hari, batuk berdahak" maxlength="500">{{ old('keluhan') }}</textarea>
                        <p class="mt-1.5 text-[11.5px] text-ink/40">
                            Keluhan hanya membantu petugas mengarahkan, bukan untuk diagnosis.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-line pt-5 sm:flex-row sm:items-center">
                    <button type="submit" class="tombol tombol-utama group">
                        <x-ikon nama="ticket" kelas="h-[18px] w-[18px]" />
                        Ambil nomor antrian
                        <x-ikon nama="arrow-right" kelas="h-4 w-4 transition group-hover:translate-x-0.5" />
                    </button>
                    <p class="text-[12px] leading-relaxed text-ink/45 sm:flex-1">
                        Dengan mendaftar, Anda menyetujui data digunakan untuk keperluan administrasi kunjungan.
                    </p>
                </div>
            </form>
        </div>

        {{-- Sisi informasi --}}
        <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
            <div class="overflow-hidden rounded-[26px] bg-ink p-7 text-white">
                <div class="flex items-center gap-2.5">
                    <span class="dot-hidup"></span>
                    <span class="text-[11px] font-bold uppercase tracking-[0.16em] text-teal-soft/70">Antrian hari ini</span>
                </div>
                <div class="mt-5 space-y-3">
                    @foreach ($polis as $p)
                        <div class="flex items-center justify-between border-b border-white/8 pb-2.5 last:border-0">
                            <span class="text-[13px] text-white/70">{{ $p->nama }}</span>
                            <span class="font-display text-[16px] font-semibold">{{ $antrianHariIni[$p->id] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('antrian.layar') }}" class="mt-6 flex items-center justify-center gap-2 rounded-xl border border-white/12 py-2.5 text-[12.5px] font-semibold text-white/70 transition hover:border-white/30 hover:text-white">
                    <x-ikon nama="monitor" kelas="h-4 w-4" /> Lihat layar panggilan
                </a>
            </div>

            <div class="kartu p-6">
                <h2 class="flex items-center gap-2 text-[14.5px] font-semibold text-ink">
                    <x-ikon nama="clipboard" kelas="h-4 w-4 text-teal" /> Jangan lupa dibawa
                </h2>
                <ul class="mt-3 space-y-2 text-[13px] leading-relaxed text-ink/60">
                    <li class="flex gap-2"><span class="text-teal">·</span> KTP atau Kartu Keluarga</li>
                    <li class="flex gap-2"><span class="text-teal">·</span> Kartu BPJS/KIS bila ada</li>
                    <li class="flex gap-2"><span class="text-teal">·</span> Surat rujukan untuk layanan tertentu</li>
                    <li class="flex gap-2"><span class="text-teal">·</span> Kode tiket antrian (screenshot boleh)</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-coral/25 bg-coral-soft/40 p-5">
                <p class="flex items-center gap-2 text-[13px] font-semibold text-[#a54d24]">
                    <x-ikon nama="shield" kelas="h-4 w-4" /> Catatan penting
                </p>
                <p class="mt-2 text-[12.5px] leading-relaxed text-[#8f451f]">
                    Bila kondisi darurat (sesak berat, kejang, perdarahan), langsung datang ke IGD
                    atau hubungi 119 — jangan menunggu nomor antrian.
                </p>
            </div>
        </aside>
    </div>
</div>
@endsection
