@php
    $menu = [
        ['rute' => 'panel.dasbor', 'ikon' => 'grid', 'label' => 'Dasbor', 'aktif' => 'panel.dasbor'],
        ['rute' => 'panel.antrian.index', 'ikon' => 'ticket', 'label' => 'Antrian', 'aktif' => 'panel.antrian.*'],
    ];
    $menuKonten = [
        ['rute' => 'panel.poli.index', 'ikon' => 'layers', 'label' => 'Poli', 'aktif' => 'panel.poli.*'],
        ['rute' => 'panel.layanan.index', 'ikon' => 'stethoscope', 'label' => 'Layanan', 'aktif' => 'panel.layanan.*'],
        ['rute' => 'panel.dokter.index', 'ikon' => 'users', 'label' => 'Tenaga kesehatan', 'aktif' => 'panel.dokter.*'],
        ['rute' => 'panel.jadwal.index', 'ikon' => 'calendar', 'label' => 'Jadwal praktik', 'aktif' => 'panel.jadwal.*'],
        ['rute' => 'panel.pengumuman.index', 'ikon' => 'megaphone', 'label' => 'Pengumuman', 'aktif' => 'panel.pengumuman.*'],
        ['rute' => 'panel.faq.index', 'ikon' => 'help', 'label' => 'FAQ', 'aktif' => 'panel.faq.*'],
    ];
    $menuSistem = [['rute' => 'panel.pengaturan.index', 'ikon' => 'settings', 'label' => 'Pengaturan', 'aktif' => 'panel.pengaturan.*']];
    if (auth()->user()?->isAdmin()) {
        $menuSistem[] = ['rute' => 'panel.pengguna.index', 'ikon' => 'shield', 'label' => 'Pengguna', 'aktif' => 'panel.pengguna.*'];
        $menuSistem[] = ['rute' => 'panel.log.index', 'ikon' => 'activity', 'label' => 'Log aktivitas', 'aktif' => 'panel.log.*'];
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('judul', 'Panel') — SILAKES</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="antialiased" x-data="{ sidebar: false }">
<div class="min-h-screen lg:flex">

    {{-- Overlay seluler --}}
    <div x-show="sidebar" x-cloak @click="sidebar = false"
         class="fixed inset-0 z-30 bg-ink/40 backdrop-blur-sm lg:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 flex w-[268px] flex-col bg-ink transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex h-[72px] shrink-0 items-center gap-3 border-b border-white/8 px-5">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-teal text-white">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <path d="M12 4v16M4 12h16"/>
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate font-display text-[17px] font-semibold leading-tight text-white">SILAKES</p>
                <p class="truncate text-[10px] font-semibold uppercase tracking-[0.14em] text-white/35">Panel pengelola</p>
            </div>
            <button @click="sidebar = false" class="grid h-8 w-8 place-items-center rounded-lg text-white/40 lg:hidden">
                <x-ikon nama="x" kelas="h-4 w-4" />
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
            <div class="space-y-1">
                @foreach ($menu as $m)
                    <a href="{{ route($m['rute']) }}" class="sidebar-taut {{ request()->routeIs($m['aktif']) ? 'aktif' : '' }}">
                        <x-ikon :nama="$m['ikon']" kelas="h-[18px] w-[18px] shrink-0" />
                        {{ $m['label'] }}
                    </a>
                @endforeach
            </div>

            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-white/25">Konten</p>
                <div class="space-y-1">
                    @foreach ($menuKonten as $m)
                        <a href="{{ route($m['rute']) }}" class="sidebar-taut {{ request()->routeIs($m['aktif']) ? 'aktif' : '' }}">
                            <x-ikon :nama="$m['ikon']" kelas="h-[18px] w-[18px] shrink-0" />
                            {{ $m['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.14em] text-white/25">Sistem</p>
                <div class="space-y-1">
                    @foreach ($menuSistem as $m)
                        <a href="{{ route($m['rute']) }}" class="sidebar-taut {{ request()->routeIs($m['aktif']) ? 'aktif' : '' }}">
                            <x-ikon :nama="$m['ikon']" kelas="h-[18px] w-[18px] shrink-0" />
                            {{ $m['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>

        <div class="shrink-0 border-t border-white/8 p-3">
            <a href="{{ route('antrian.layar') }}" target="_blank"
               class="sidebar-taut !text-white/50 hover:!text-white">
                <x-ikon nama="monitor" kelas="h-[18px] w-[18px] shrink-0" />
                Buka layar panggilan
                <x-ikon nama="arrow-up-right" kelas="ml-auto h-3.5 w-3.5" />
            </a>
            <div class="mt-2 flex items-center gap-3 rounded-xl bg-white/[0.05] px-3 py-2.5">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-teal/25 text-[12px] font-bold text-teal-soft">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-[12.5px] font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-[10.5px] text-white/35">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-white/35 transition hover:bg-white/10 hover:text-coral" title="Keluar">
                        <x-ikon nama="logout" kelas="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex h-[72px] items-center gap-4 border-b border-line bg-cream/90 px-5 backdrop-blur-xl sm:px-8">
            <button @click="sidebar = true" class="grid h-10 w-10 place-items-center rounded-xl border border-line lg:hidden">
                <x-ikon nama="menu" kelas="h-5 w-5" />
            </button>

            <div class="min-w-0 flex-1">
                <p class="truncate text-[11px] font-bold uppercase tracking-[0.13em] text-ink/35">@yield('remah', 'Panel')</p>
                <p class="truncate text-[16px] font-semibold text-ink">@yield('judul', 'Panel')</p>
            </div>

            <a href="{{ route('beranda') }}" target="_blank"
               class="hidden items-center gap-2 rounded-full border border-line bg-white px-4 py-2 text-[12.5px] font-semibold text-ink/70 transition hover:border-teal hover:text-teal sm:inline-flex">
                <x-ikon nama="arrow-up-right" kelas="h-3.5 w-3.5" /> Lihat situs
            </a>
        </header>

        {{-- Notifikasi --}}
        @if (session('sukses') || session('info'))
            <div class="px-5 pt-5 sm:px-8">
                <div class="flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-sm
                    {{ session('sukses') ? 'border-teal/25 bg-teal-soft/60 text-pine' : 'border-coral/25 bg-coral-soft/60 text-[#9c4a22]' }}">
                    <x-ikon :nama="session('sukses') ? 'check-bulat' : 'bell'" kelas="mt-0.5 h-4 w-4 shrink-0" />
                    <p class="font-medium">{{ session('sukses') ?? session('info') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="px-5 pt-5 sm:px-8">
                <div class="rounded-2xl border border-coral/25 bg-coral-soft/50 px-4 py-3.5">
                    <p class="text-[13px] font-semibold text-[#a54d24]">Ada yang perlu diperbaiki:</p>
                    <ul class="mt-1.5 space-y-0.5 text-[13px] text-[#8f451f]">
                        @foreach ($errors->all() as $e)
                            <li>· {{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <main class="flex-1 px-5 py-6 sm:px-8 sm:py-8">
            @yield('konten')
        </main>

        <footer class="border-t border-line px-5 py-4 text-[11.5px] text-ink/35 sm:px-8">
            SILAKES · Puskesmas Umban Sari — panel pengelola layanan
        </footer>
    </div>
</div>
@stack('skrip')
</body>
</html>
