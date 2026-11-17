@extends('layouts.publik')

@section('judul', 'Beranda')
@section('deskripsi', 'Layanan kesehatan Puskesmas Umban Sari: poli, jadwal dokter, alur pendaftaran, dan antrian online.')

@section('konten')
@php
    $sedangDipanggil = collect($antrianBerjalan)->flatten(1)->firstWhere('status', 'dipanggil');
    $menungguTotal = collect($antrianBerjalan)->flatten(1)->where('status', 'menunggu')->count();
    $jamBuka = \App\Models\Pengaturan::get('jam_buka', '08:00');
    $jamTutup = \App\Models\Pengaturan::get('jam_tutup', '14:00');
@endphp

{{-- ============ HERO ============ --}}
<section class="grain relative overflow-hidden">
    {{-- ornamen latar --}}
    <div class="pointer-events-none absolute -right-40 -top-40 h-[520px] w-[520px] rounded-full bg-teal-soft/50 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-32 top-64 h-[380px] w-[380px] rounded-full bg-coral-soft/40 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-5 pb-16 pt-14 sm:px-8 sm:pt-20 lg:pb-24">
        <div class="grid items-center gap-14 lg:grid-cols-[1.05fr_0.95fr] lg:gap-10">

            {{-- Kolom teks --}}
            <div class="naik-masuk">
                <div class="inline-flex items-center gap-2.5 rounded-full border border-line bg-white/70 py-1.5 pl-1.5 pr-4 backdrop-blur">
                    <span class="grid h-6 w-6 place-items-center rounded-full bg-teal text-white">
                        <x-ikon nama="leaf" kelas="h-3.5 w-3.5" />
                    </span>
                    <span class="text-[11.5px] font-bold uppercase tracking-[0.13em] text-ink/60">Puskesmas Umban Sari · Pekanbaru</span>
                </div>

                <h1 class="mt-7 font-display text-[42px] font-medium leading-[1.04] tracking-[-0.03em] text-ink sm:text-[56px] lg:text-[62px]">
                    Layanan sehat<br>
                    yang <span class="italic text-teal">dekat</span> dan<br>
                    <span class="relative inline-block">
                        manusiawi.
                        <svg class="absolute -bottom-1 left-0 w-full" height="10" viewBox="0 0 300 10" fill="none" preserveAspectRatio="none">
                            <path d="M2 7.5C60 3 140 2.5 298 6" stroke="#e2703f" stroke-width="3" stroke-linecap="round" opacity="0.5"/>
                        </svg>
                    </span>
                </h1>

                <p class="mt-7 max-w-lg text-[15.5px] leading-relaxed text-ink/60">
                    Ambil nomor antrian dari rumah, lihat jadwal dokter yang praktik hari ini, dan ketahui
                    persyaratan layanan sebelum berangkat. Tanpa menunggu lama, tanpa bolak-balik.
                </p>

                <div class="mt-9 flex flex-wrap items-center gap-3">
                    <a href="{{ route('antrian.form') }}" class="tombol tombol-utama group !px-6 !py-3.5">
                        <x-ikon nama="ticket" kelas="h-[18px] w-[18px]" />
                        Ambil nomor antrian
                        <x-ikon nama="arrow-right" kelas="h-4 w-4 transition group-hover:translate-x-0.5" />
                    </a>
                    <a href="{{ route('jadwal') }}" class="tombol tombol-garis !px-6 !py-3.5">
                        <x-ikon nama="calendar" kelas="h-[18px] w-[18px]" /> Lihat jadwal dokter
                    </a>
                </div>

                <div class="mt-10 grid grid-cols-3 gap-4 border-t border-line pt-6">
                    <div>
                        <p class="font-display text-2xl font-semibold text-ink">{{ $totalLayanan }}</p>
                        <p class="text-[11.5px] font-semibold uppercase tracking-[0.1em] text-ink/40">Jenis layanan</p>
                    </div>
                    <div class="border-l border-line pl-4">
                        <p class="font-display text-2xl font-semibold text-ink">{{ $totalDokter }}</p>
                        <p class="text-[11.5px] font-semibold uppercase tracking-[0.1em] text-ink/40">Tenaga kesehatan</p>
                    </div>
                    <div class="border-l border-line pl-4">
                        <p class="font-display text-2xl font-semibold text-ink">{{ $antrianHariIni }}</p>
                        <p class="text-[11.5px] font-semibold uppercase tracking-[0.1em] text-ink/40">Antrian hari ini</p>
                    </div>
                </div>
            </div>

            {{-- Kartu antrian langsung --}}
            <div class="naik-masuk relative" style="animation-delay:.12s">
                <div class="relative overflow-hidden rounded-[28px] bg-ink p-1.5 shadow-[0_40px_80px_-40px_rgba(12,39,35,0.7)]">
                    <div class="relative overflow-hidden rounded-[22px] bg-gradient-to-b from-[#0f312b] to-[#0a211d] px-7 pb-7 pt-6">

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="dot-hidup"></span>
                                <span class="text-[11px] font-bold uppercase tracking-[0.16em] text-teal-soft/70">Antrian langsung</span>
                            </div>
                            <span class="rounded-full bg-white/10 px-2.5 py-1 text-[10.5px] font-semibold text-white/70">
                                {{ now()->format('H:i') }} WIB
                            </span>
                        </div>

                        {{-- Nomor yang dipanggil --}}
                        <div class="mt-7">
                            <p class="text-[11.5px] font-medium uppercase tracking-[0.12em] text-white/35">Sedang dilayani</p>
                            @if ($sedangDipanggil)
                                <div class="mt-2 flex items-end gap-3">
                                    <p class="font-display text-[64px] font-semibold leading-none tracking-tight text-white">{{ $sedangDipanggil->kode }}</p>
                                </div>
                                <p class="mt-2.5 text-sm text-teal-soft/75">{{ $sedangDipanggil->poli->nama }}</p>
                            @else
                                <div class="mt-2 flex items-end gap-3">
                                    <p class="font-display text-[64px] font-semibold leading-none tracking-tight text-white/20">—</p>
                                </div>
                                <p class="mt-2.5 text-sm text-teal-soft/50">Belum ada panggilan</p>
                            @endif
                        </div>

                        {{-- Garis tiket --}}
                        <div class="my-6 border-t border-dashed border-white/12"></div>

                        {{-- Daftar poli --}}
                        <div class="space-y-2.5">
                            @forelse ($polis as $poli)
                                @php
                                    $grup = $antrianBerjalan[$poli->id] ?? collect();
                                    $sekarang = $grup->firstWhere('status', 'dipanggil');
                                    $berikutnya = $grup->where('status', 'menunggu')->sortBy('nomor')->first();
                                    $tunggu = $grup->where('status', 'menunggu')->count();
                                @endphp
                                <div class="flex items-center gap-3 rounded-xl bg-white/[0.045] px-3.5 py-2.5 ring-1 ring-inset ring-white/[0.06]">
                                    <span class="h-1.5 w-1.5 shrink-0 rounded-full" style="background: {{ $poli->warna }}"></span>
                                    <span class="flex-1 truncate text-[13px] font-medium text-white/80">{{ $poli->nama }}</span>
                                    <span class="font-display text-[15px] font-semibold {{ $sekarang ? 'text-white' : 'text-white/30' }}">{{ $sekarang->kode ?? $berikutnya->kode ?? '—' }}</span>
                                    <span class="w-16 text-right text-[11px] text-white/50">{{ $tunggu }} tunggu</span>
                                </div>
                            @empty
                                <p class="text-sm text-white/40">Belum ada poli aktif.</p>
                            @endforelse
                        </div>

                        <a href="{{ route('antrian.layar') }}"
                           class="mt-6 flex items-center justify-center gap-2 rounded-xl border border-white/12 py-2.5 text-[13px] font-semibold text-white/70 transition hover:border-white/30 hover:text-white">
                            <x-ikon nama="monitor" kelas="h-4 w-4" /> Buka layar panggilan
                        </a>
                    </div>
                </div>

                {{-- Label kecil di luar kartu --}}
                <div class="absolute -bottom-4 -left-3 hidden items-center gap-2 rounded-xl border border-line bg-white px-3.5 py-2.5 shadow-lg sm:flex">
                    <x-ikon nama="clock" kelas="h-4 w-4 text-teal" />
                    <span class="text-[12px] font-semibold text-ink">{{ $jamBuka }}–{{ $jamTutup }}</span>
                    <span class="text-[11px] text-ink/45">{{ \App\Models\Pengaturan::get('hari_kerja', 'Senin–Sabtu') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ POLI ============ --}}
<section class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div class="max-w-xl">
            <p class="eyebrow">Unit layanan</p>
            <h2 class="mt-3 text-[34px] leading-tight text-ink sm:text-[40px]">Poli yang siap melayani Anda</h2>
            <p class="mt-3 text-[15px] leading-relaxed text-ink/55">
                Setiap poli punya alur dan persyaratannya sendiri. Pilih poli untuk melihat layanan,
                jadwal, dan apa saja yang perlu dibawa.
            </p>
        </div>
        <a href="{{ route('layanan.index') }}" class="tombol tombol-garis group">
            Semua layanan <x-ikon nama="arrow-right" kelas="h-4 w-4 transition group-hover:translate-x-0.5" />
        </a>
    </div>

    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($polis as $i => $poli)
            <a href="{{ route('layanan.index') }}#{{ $poli->slug }}"
               class="kartu kartu-naik group relative overflow-hidden p-6">
                <span class="absolute right-0 top-0 h-24 w-24 rounded-bl-[60px] opacity-[0.07] transition group-hover:opacity-[0.12]"
                      style="background: {{ $poli->warna }}"></span>

                <div class="flex items-start justify-between">
                    <span class="grid h-12 w-12 place-items-center rounded-2xl text-white shadow-sm"
                          style="background: {{ $poli->warna }}">
                        <x-ikon :nama="$poli->ikon ?: 'stethoscope'" kelas="h-6 w-6" />
                    </span>
                    <span class="font-display text-[13px] font-semibold text-ink/20">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                <h3 class="mt-5 text-[19px] font-semibold text-ink">{{ $poli->nama }}</h3>
                <p class="mt-2 line-clamp-2 text-[13.5px] leading-relaxed text-ink/55">
                    {{ $poli->deskripsi ?: 'Layanan kesehatan dasar di poli ' . $poli->nama . '.' }}
                </p>

                <div class="mt-5 flex items-center gap-1.5 text-[12.5px] font-semibold text-teal">
                    Lihat layanan
                    <x-ikon nama="arrow-up-right" kelas="h-3.5 w-3.5 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                </div>
            </a>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-line p-8 text-center text-sm text-ink/50">
                Data poli belum tersedia.
            </p>
        @endforelse
    </div>
</section>

{{-- ============ ALUR ============ --}}
<section class="relative overflow-hidden border-y border-line bg-white">
    <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
        <div class="grid gap-14 lg:grid-cols-[0.85fr_1.15fr]">
            <div>
                <p class="eyebrow">Alur kunjungan</p>
                <h2 class="mt-3 text-[34px] leading-tight text-ink sm:text-[40px]">
                    Empat langkah,<br>tanpa bingung.
                </h2>
                <p class="mt-4 max-w-sm text-[15px] leading-relaxed text-ink/55">
                    Dari mengambil nomor sampai menerima obat, semuanya sudah punya urutan yang jelas.
                </p>
                <a href="{{ route('alur') }}" class="tombol tombol-utama mt-8 group">
                    Detail alur lengkap <x-ikon nama="arrow-right" kelas="h-4 w-4 transition group-hover:translate-x-0.5" />
                </a>
            </div>

            <div class="relative">
                <div class="absolute left-[27px] top-4 bottom-4 w-px bg-gradient-to-b from-teal/40 via-line to-transparent"></div>
                <div class="space-y-7">
                    @foreach ([
                        ['ticket', 'Ambil nomor antrian', 'Dari rumah lewat website, atau di loket saat tiba.'],
                        ['clipboard', 'Daftar & verifikasi', 'Tunjukkan kode tiket dan dokumen identitas.'],
                        ['stethoscope', 'Pemeriksaan di poli', 'Nomor Anda dipanggil lewat layar ruang tunggu.'],
                        ['pill', 'Farmasi & selesai', 'Ambil obat, atau terima surat rujukan bila perlu.'],
                    ] as $i => $langkah)
                        <div class="relative flex gap-5">
                            <span class="relative z-10 grid h-14 w-14 shrink-0 place-items-center rounded-2xl border border-line bg-cream text-teal shadow-sm">
                                <x-ikon :nama="$langkah[0]" kelas="h-6 w-6" />
                                <span class="absolute -right-1.5 -top-1.5 grid h-5 w-5 place-items-center rounded-full bg-ink text-[10px] font-bold text-white">{{ $i + 1 }}</span>
                            </span>
                            <div class="pt-2.5">
                                <h3 class="text-[16.5px] font-semibold text-ink">{{ $langkah[1] }}</h3>
                                <p class="mt-1 text-[13.5px] leading-relaxed text-ink/55">{{ $langkah[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ JADWAL HARI INI ============ --}}
<section class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
    <div class="grid gap-10 lg:grid-cols-[1fr_1.3fr] lg:gap-16">
        <div>
            <p class="eyebrow">Jadwal praktik</p>
            <h2 class="mt-3 text-[34px] leading-tight text-ink sm:text-[40px]">
                Siapa yang praktik<br><span class="italic text-teal">{{ now()->translatedFormat('l') }}</span> ini?
            </h2>
            <p class="mt-4 max-w-sm text-[15px] leading-relaxed text-ink/55">
                Jadwal bisa berubah pada hari libur nasional. Konfirmasi lewat telepon bila ragu.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('jadwal') }}" class="tombol tombol-garis group">
                    Jadwal seminggu <x-ikon nama="arrow-right" kelas="h-4 w-4 transition group-hover:translate-x-0.5" />
                </a>
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', \App\Models\Pengaturan::get('telepon', '0761000000')) }}" class="tombol tombol-garis">
                    <x-ikon nama="phone" kelas="h-4 w-4" /> Hubungi
                </a>
            </div>
        </div>

        <div class="kartu overflow-hidden">
            <div class="flex items-center justify-between border-b border-line bg-sand/40 px-6 py-4">
                <p class="text-[13px] font-bold uppercase tracking-[0.1em] text-ink/55">{{ now()->translatedFormat('l, d F Y') }}</p>
                @if ($statusBuka)
                    <span class="lencana bg-teal-soft text-pine"><span class="dot-hidup !h-1.5 !w-1.5"></span> Buka</span>
                @else
                    <span class="lencana bg-sand text-ink/50">Tutup</span>
                @endif
            </div>

            @forelse ($jadwalHariIni as $j)
                <div class="flex items-center gap-4 border-b border-line/60 px-6 py-4 last:border-0">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-ink/[0.04] font-display text-[15px] font-semibold text-ink/70">
                        {{ $j->dokter->inisial() }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[14.5px] font-semibold text-ink">{{ $j->dokter->namaLengkap() }}</p>
                        <p class="mt-0.5 truncate text-[12.5px] text-ink/50">{{ $j->poli->nama ?? 'Umum' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-display text-[15px] font-semibold text-ink">{{ $j->jam }}</p>
                        <p class="text-[11px] text-ink/40">kuota {{ $j->kuota }}</p>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <x-ikon nama="calendar" kelas="mx-auto h-8 w-8 text-ink/20" />
                    <p class="mt-3 text-sm text-ink/50">Tidak ada jadwal praktik hari ini.</p>
                    <a href="{{ route('jadwal') }}" class="mt-3 inline-block text-[13px] font-semibold text-teal hover:underline">Lihat jadwal hari lain</a>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ============ LAYANAN UNGGULAN ============ --}}
@if ($layananUnggulan->isNotEmpty())
<section class="border-y border-line bg-sand/30">
    <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div class="max-w-xl">
                <p class="eyebrow">Layanan</p>
                <h2 class="mt-3 text-[34px] leading-tight text-ink sm:text-[40px]">Yang paling sering dicari warga</h2>
            </div>
            <a href="{{ route('layanan.index') }}" class="tombol tombol-garis group">
                Semua layanan <x-ikon nama="arrow-right" kelas="h-4 w-4 transition group-hover:translate-x-0.5" />
            </a>
        </div>

        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($layananUnggulan as $l)
                <a href="{{ route('layanan.detail', $l) }}" class="kartu kartu-naik group flex flex-col p-6">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full" style="background: {{ $l->poli->warna ?? '#12796A' }}"></span>
                        <span class="text-[11px] font-bold uppercase tracking-[0.11em] text-ink/40">{{ $l->poli->nama ?? 'Layanan' }}</span>
                    </div>
                    <h3 class="mt-3 text-[17.5px] font-semibold leading-snug text-ink group-hover:text-teal">{{ $l->nama }}</h3>
                    <p class="mt-2 flex-1 text-[13.5px] leading-relaxed text-ink/55">{{ $l->ringkasan }}</p>
                    <div class="mt-5 flex items-center justify-between border-t border-line/70 pt-4 text-[12px]">
                        <span class="flex items-center gap-1.5 text-ink/45"><x-ikon nama="clock" kelas="h-3.5 w-3.5" /> ±{{ $l->durasi_menit }} menit</span>
                        @if ($l->butuh_rujukan)
                            <span class="lencana bg-coral-soft text-[#a54d24]">Butuh rujukan</span>
                        @else
                            <span class="lencana bg-teal-soft/70 text-pine">Tanpa rujukan</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ PENGUMUMAN + FAQ ============ --}}
<section class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
    <div class="grid gap-14 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Kabar puskesmas</p>
                    <h2 class="mt-3 text-[30px] leading-tight text-ink">Pengumuman terbaru</h2>
                </div>
                <a href="{{ route('pengumuman.index') }}" class="text-[13px] font-semibold text-teal hover:underline">Semua</a>
            </div>

            <div class="mt-7 space-y-3">
                @forelse ($pengumuman as $p)
                    <a href="{{ route('pengumuman.detail', $p) }}" class="kartu kartu-naik group flex gap-4 p-5">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-teal-soft/60 text-teal">
                            <x-ikon nama="megaphone" kelas="h-5 w-5" />
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.1em] text-ink/40">
                                <span>{{ $p->labelKategori() }}</span>
                                <span class="h-0.5 w-0.5 rounded-full bg-ink/25"></span>
                                <span>{{ $p->published_at?->translatedFormat('d M Y') }}</span>
                            </div>
                            <h3 class="mt-1.5 text-[15.5px] font-semibold leading-snug text-ink group-hover:text-teal">{{ $p->judul }}</h3>
                            <p class="mt-1 line-clamp-2 text-[13px] leading-relaxed text-ink/50">{{ $p->ringkasan }}</p>
                        </div>
                    </a>
                @empty
                    <p class="rounded-2xl border border-dashed border-line p-8 text-center text-sm text-ink/50">Belum ada pengumuman.</p>
                @endforelse
            </div>
        </div>

        <div>
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Tanya jawab</p>
                    <h2 class="mt-3 text-[30px] leading-tight text-ink">Sering ditanyakan</h2>
                </div>
                <a href="{{ route('faq') }}" class="text-[13px] font-semibold text-teal hover:underline">Semua</a>
            </div>

            <div class="mt-7 space-y-2.5" x-data="{ buka: 0 }">
                @foreach ($faqSingkat as $i => $f)
                    <div class="kartu overflow-hidden">
                        <button @click="buka = buka === {{ $i + 1 }} ? 0 : {{ $i + 1 }}"
                                class="flex w-full items-center gap-3 px-5 py-4 text-left">
                            <span class="flex-1 text-[14px] font-semibold leading-snug text-ink">{{ $f->pertanyaan }}</span>
                            <x-ikon nama="chevron-down" kelas="h-4 w-4 shrink-0 text-ink/40 transition" x-bind:class="buka === {{ $i + 1 }} && 'rotate-180'" />
                        </button>
                        <div x-show="buka === {{ $i + 1 }}" x-transition x-cloak>
                            <p class="border-t border-line/70 px-5 py-4 text-[13px] leading-relaxed text-ink/60">{{ $f->jawaban }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="mx-auto max-w-7xl px-5 pb-4 sm:px-8">
    <div class="relative overflow-hidden rounded-[32px] bg-ink px-8 py-14 sm:px-14">
        <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-teal/25 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-coral/15 blur-3xl"></div>

        <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
            <div class="max-w-xl">
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-teal-soft/60">Tidak perlu antre pagi-pagi</p>
                <h2 class="mt-4 font-display text-[34px] leading-[1.1] text-white sm:text-[42px]">
                    Ambil nomor sekarang,<br>datang tepat waktu.
                </h2>
                <p class="mt-4 text-[15px] leading-relaxed text-white/50">
                    Nomor antrian online dibuka setiap hari praktik. Simpan kode tiket Anda dan tunjukkan di loket.
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row lg:flex-col">
                <a href="{{ route('antrian.form') }}" class="tombol bg-cream text-ink hover:bg-white group !px-7 !py-3.5">
                    <x-ikon nama="ticket" kelas="h-[18px] w-[18px]" /> Ambil antrian
                    <x-ikon nama="arrow-right" kelas="h-4 w-4 transition group-hover:translate-x-0.5" />
                </a>
                <a href="{{ route('kontak') }}" class="tombol border border-white/15 text-white/80 hover:border-white/35 hover:text-white !px-7 !py-3.5">
                    <x-ikon nama="map-pin" kelas="h-[18px] w-[18px]" /> Lokasi & kontak
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
