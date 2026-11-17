@php
    use App\Models\Pengaturan;
    $namaPuskesmas = Pengaturan::get('nama_puskesmas', 'Puskesmas Umban Sari');
    $telepon = Pengaturan::get('telepon', '(0761) 000000');
    $alamat = Pengaturan::get('alamat', 'Jl. Umban Sari, Rumbai, Pekanbaru, Riau');
    $email = Pengaturan::get('email', 'puskesmas.umbansari@pku.go.id');
    $jam = Pengaturan::get('jam_operasional', 'Senin–Sabtu, 08.00–14.00');
    $menu = [
        ['label' => 'Layanan', 'rute' => 'layanan.index', 'aktif' => 'layanan.*'],
        ['label' => 'Jadwal', 'rute' => 'jadwal', 'aktif' => 'jadwal'],
        ['label' => 'Alur', 'rute' => 'alur', 'aktif' => 'alur'],
        ['label' => 'Pengumuman', 'rute' => 'pengumuman.index', 'aktif' => 'pengumuman.*'],
        ['label' => 'FAQ', 'rute' => 'faq', 'aktif' => 'faq'],
        ['label' => 'Kontak', 'rute' => 'kontak', 'aktif' => 'kontak'],
    ];
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', 'Beranda') — SILAKES {{ $namaPuskesmas }}</title>
    <meta name="description" content="@yield('deskripsi', 'Sistem Informasi Layanan Kesehatan ' . $namaPuskesmas . '. Lihat layanan, jadwal dokter, dan ambil nomor antrian online.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased">

{{-- Bilah atas --}}
<header x-data="{ buka: false }" class="sticky top-0 z-40 border-b border-line/70 bg-cream/85 backdrop-blur-xl">
    <div class="mx-auto flex h-[72px] max-w-7xl items-center gap-8 px-5 sm:px-8">
        <a href="{{ route('beranda') }}" class="group flex items-center gap-3">
            <span class="relative grid h-10 w-10 place-items-center rounded-[13px] bg-ink text-cream shadow-sm transition group-hover:bg-teal">
                <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round">
                    <path d="M12 4v16M4 12h16"/>
                </svg>
            </span>
            <span class="leading-tight">
                <span class="block font-display text-[19px] font-semibold tracking-tight text-ink">SILAKES</span>
                <span class="block text-[10.5px] font-semibold uppercase tracking-[0.14em] text-ink/45">Umban Sari</span>
            </span>
        </a>

        <nav class="hidden items-center gap-7 lg:flex">
            @foreach ($menu as $m)
                <a href="{{ route($m['rute']) }}"
                   class="nav-taut {{ request()->routeIs($m['aktif']) ? 'aktif' : '' }}">{{ $m['label'] }}</a>
            @endforeach
        </nav>

        <div class="ml-auto flex items-center gap-3">
            @if (\App\Http\Controllers\Publik\BerandaController::statusBuka())
                <span class="hidden items-center gap-2 rounded-full bg-teal-soft/70 px-3 py-1.5 text-[11.5px] font-semibold text-pine sm:inline-flex">
                    <span class="dot-hidup"></span> Buka sekarang
                </span>
            @else
                <span class="hidden items-center gap-2 rounded-full bg-sand px-3 py-1.5 text-[11.5px] font-semibold text-ink/55 sm:inline-flex">
                    <span class="h-2 w-2 rounded-full bg-ink/30"></span> Tutup
                </span>
            @endif

            <a href="{{ route('antrian.form') }}" class="tombol tombol-utama hidden !min-h-10 !px-5 !py-2 !text-[13.5px] sm:inline-flex">
                <x-ikon nama="ticket" kelas="h-4 w-4" /> Ambil Antrian
            </a>

            @auth
                <a href="{{ route('panel.dasbor') }}" class="tombol tombol-garis !min-h-10 !px-4 !py-2 !text-[13.5px]">Panel</a>
            @else
                <a href="{{ route('login') }}" class="hidden text-[13.5px] font-semibold text-ink/60 transition hover:text-ink md:block">Masuk</a>
            @endauth

            <button @click="buka = !buka" class="grid h-10 w-10 place-items-center rounded-xl border border-line text-ink lg:hidden" aria-label="Buka menu">
                <x-ikon nama="menu" kelas="h-5 w-5" x-show="!buka" />
                <x-ikon nama="x" kelas="h-5 w-5" x-show="buka" x-cloak />
            </button>
        </div>
    </div>

    {{-- Menu seluler --}}
    <div x-show="buka" x-cloak x-transition.opacity class="border-t border-line bg-cream px-5 pb-5 pt-2 lg:hidden">
        @foreach ($menu as $m)
            <a href="{{ route($m['rute']) }}" class="block border-b border-line/60 py-3 text-[15px] font-medium text-ink/80">{{ $m['label'] }}</a>
        @endforeach
        <div class="mt-4 flex gap-3">
            <a href="{{ route('antrian.form') }}" class="tombol tombol-utama flex-1 !py-2.5 !text-sm">Ambil Antrian</a>
            <a href="{{ auth()->check() ? route('panel.dasbor') : route('login') }}" class="tombol tombol-garis flex-1 !py-2.5 !text-sm">{{ auth()->check() ? 'Panel' : 'Masuk' }}</a>
        </div>
    </div>
</header>

{{-- Notifikasi --}}
@if (session('sukses') || session('info'))
    <div class="mx-auto mt-5 max-w-7xl px-5 sm:px-8">
        <div class="flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-sm
            {{ session('sukses') ? 'border-teal/25 bg-teal-soft/60 text-pine' : 'border-coral/25 bg-coral-soft/60 text-[#9c4a22]' }}">
            <x-ikon nama="check-bulat" kelas="mt-0.5 h-4.5 w-4.5 shrink-0" />
            <p class="font-medium">{{ session('sukses') ?? session('info') }}</p>
        </div>
    </div>
@endif

<main>
    @yield('konten')
</main>

{{-- Footer --}}
<footer class="mt-24 border-t border-line bg-white">
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
        <div class="grid gap-12 md:grid-cols-[1.4fr_1fr_1fr_1.1fr]">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-[13px] bg-ink text-cream">
                        <svg viewBox="0 0 24 24" class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round">
                            <path d="M12 4v16M4 12h16"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-display text-lg font-semibold text-ink">SILAKES</p>
                        <p class="text-[10.5px] font-semibold uppercase tracking-[0.14em] text-ink/45">{{ $namaPuskesmas }}</p>
                    </div>
                </div>
                <p class="mt-5 max-w-xs text-sm leading-relaxed text-ink/55">
                    Sistem informasi layanan kesehatan untuk memudahkan warga mengakses layanan, jadwal dokter,
                    dan antrian tanpa harus menunggu lama di loket.
                </p>
            </div>

            <div>
                <p class="eyebrow">Jelajahi</p>
                <ul class="mt-4 space-y-2.5 text-sm text-ink/60">
                    @foreach ($menu as $m)
                        <li><a href="{{ route($m['rute']) }}" class="transition hover:text-teal">{{ $m['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div>
                <p class="eyebrow">Layanan Cepat</p>
                <ul class="mt-4 space-y-2.5 text-sm text-ink/60">
                    <li><a href="{{ route('antrian.form') }}" class="transition hover:text-teal">Ambil nomor antrian</a></li>
                    <li><a href="{{ route('antrian.layar') }}" class="transition hover:text-teal">Layar panggilan</a></li>
                    <li><a href="{{ route('alur') }}" class="transition hover:text-teal">Alur pendaftaran</a></li>
                    <li><a href="{{ route('login') }}" class="transition hover:text-teal">Masuk panel petugas</a></li>
                </ul>
            </div>

            <div>
                <p class="eyebrow">Kunjungi</p>
                <ul class="mt-4 space-y-3 text-sm text-ink/60">
                    <li class="flex gap-2.5"><x-ikon nama="map-pin" kelas="mt-0.5 h-4 w-4 shrink-0 text-teal" /><span>{{ $alamat }}</span></li>
                    <li class="flex gap-2.5"><x-ikon nama="phone" kelas="mt-0.5 h-4 w-4 shrink-0 text-teal" /><span>{{ $telepon }}</span></li>
                    <li class="flex gap-2.5"><x-ikon nama="mail" kelas="mt-0.5 h-4 w-4 shrink-0 text-teal" /><span>{{ $email }}</span></li>
                    <li class="flex gap-2.5"><x-ikon nama="clock" kelas="mt-0.5 h-4 w-4 shrink-0 text-teal" /><span>{{ $jam }}</span></li>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-line pt-6 text-[12.5px] text-ink/45 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $namaPuskesmas }}. Dinas Kesehatan Kota Pekanbaru.</p>
            <p class="flex items-center gap-1.5">
                <x-ikon nama="leaf" kelas="h-3.5 w-3.5" /> Bagian dari upaya kesehatan masyarakat
            </p>
        </div>
    </div>
</footer>

@stack('skrip')
</body>
</html>
