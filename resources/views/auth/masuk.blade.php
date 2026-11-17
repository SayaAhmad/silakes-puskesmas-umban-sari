<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Panel — SILAKES</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
<div class="grid min-h-screen lg:grid-cols-[1fr_1.05fr]">

    {{-- Kiri: panel gelap --}}
    <div class="relative hidden overflow-hidden bg-ink p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="pointer-events-none absolute -right-32 -top-32 h-[420px] w-[420px] rounded-full bg-teal/25 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-20 h-[380px] w-[380px] rounded-full bg-coral/12 blur-3xl"></div>

        <a href="{{ route('beranda') }}" class="relative flex items-center gap-3">
            <span class="grid h-11 w-11 place-items-center rounded-[14px] bg-white/10 backdrop-blur">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                    <path d="M12 4v16M4 12h16"/>
                </svg>
            </span>
            <div>
                <p class="font-display text-[19px] font-semibold leading-tight">SILAKES</p>
                <p class="text-[10.5px] font-semibold uppercase tracking-[0.15em] text-white/40">Umban Sari</p>
            </div>
        </a>

        <div class="relative max-w-md">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-teal-soft/50">Panel pengelola</p>
            <h1 class="mt-5 font-display text-[42px] leading-[1.08]">
                Kelola layanan<br>dengan <span class="italic text-teal-soft">tenang.</span>
            </h1>
            <p class="mt-5 text-[14.5px] leading-relaxed text-white/45">
                Satu tempat untuk mengatur poli, jadwal dokter, pengumuman, dan memanggil antrian pasien
                secara real-time.
            </p>

            <div class="mt-10 space-y-3">
                @foreach ([
                    ['grid', 'Dasbor ringkas', 'Statistik kunjungan & antrian berjalan'],
                    ['ticket', 'Panggilan antrian', 'Panggil pasien dengan satu klik'],
                    ['calendar', 'Jadwal & poli', 'Atur praktik dokter dan unit layanan'],
                ] as $f)
                    <div class="flex items-center gap-3.5 rounded-xl bg-white/[0.045] px-4 py-3 ring-1 ring-inset ring-white/[0.06]">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-teal/20 text-teal-soft">
                            <x-ikon :nama="$f[0]" kelas="h-4 w-4" />
                        </span>
                        <div>
                            <p class="text-[13px] font-semibold">{{ $f[1] }}</p>
                            <p class="text-[11.5px] text-white/35">{{ $f[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="relative text-[11.5px] text-white/25">
            &copy; {{ now()->year }} Puskesmas Umban Sari · Dinas Kesehatan Kota Pekanbaru
        </p>
    </div>

    {{-- Kanan: formulir --}}
    <div class="flex flex-col justify-center bg-cream px-6 py-12 sm:px-12 lg:px-16">
        <div class="mx-auto w-full max-w-[400px]">
            <a href="{{ route('beranda') }}" class="mb-10 inline-flex items-center gap-2 text-[13px] font-semibold text-ink/50 transition hover:text-teal lg:hidden">
                <x-ikon nama="arrow-left" kelas="h-4 w-4" /> Kembali ke situs
            </a>

            <h2 class="text-[30px] leading-tight text-ink">Masuk ke panel</h2>
            <p class="mt-2 text-[14px] text-ink/55">Gunakan akun yang diberikan administrator.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-coral/30 bg-coral-soft/50 px-4 py-3 text-[13px] text-[#a54d24]">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.proses') }}" class="mt-7 space-y-5">
                @csrf

                <div>
                    <label for="email" class="label">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="input"
                           placeholder="nama@puskesmas.go.id" required autofocus autocomplete="username">
                </div>

                <div x-data="{ lihat: false }">
                    <label for="password" class="label">Kata sandi</label>
                    <div class="relative">
                        <input id="password" name="password" :type="lihat ? 'text' : 'password'" class="input pr-12"
                               placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" @click="lihat = !lihat"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-ink/35 transition hover:text-ink"
                                aria-label="Tampilkan kata sandi">
                            <x-ikon nama="eye" kelas="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <label class="flex cursor-pointer items-center gap-2.5 text-[13px] text-ink/60">
                    <input type="checkbox" name="ingat" value="1"
                           class="h-4 w-4 rounded border-line text-teal focus:ring-teal/30">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit" class="tombol tombol-utama w-full !py-3.5">
                    Masuk <x-ikon nama="arrow-right" kelas="h-4 w-4" />
                </button>
            </form>

            <div class="mt-8 border-t border-line pt-6">
                <p class="text-[12px] leading-relaxed text-ink/45">
                    Lupa kata sandi atau butuh akun baru? Hubungi administrator puskesmas.
                    Akses panel dicatat pada log aktivitas.
                </p>
            </div>

            <div class="mt-6 rounded-xl bg-sand/60 px-4 py-3">
                <p class="text-[11.5px] leading-relaxed text-ink/50">
                    <span class="font-semibold text-ink/70">Akun demo:</span>
                    admin@silakes.test / admin12345
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
