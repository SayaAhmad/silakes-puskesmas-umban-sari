@extends('layouts.publik')
@section('judul', 'Kontak & Lokasi')

@section('konten')
<section class="border-b border-line">
    <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 sm:py-20">
        <p class="eyebrow">Kunjungi kami</p>
        <h1 class="mt-3 max-w-2xl text-[38px] leading-[1.08] text-ink sm:text-[48px]">Kontak & lokasi</h1>
        <p class="mt-5 max-w-xl text-[15.5px] leading-relaxed text-ink/60">
            Kami siap membantu. Untuk keadaan darurat, segera hubungi layanan gawat darurat terdekat.
        </p>
    </div>
</section>

<div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['map-pin', 'Alamat', $alamat],
            ['phone', 'Telepon', $telepon],
            ['mail', 'Email', $email],
            ['clock', 'Jam layanan', $jamOperasional],
        ] as $kartu)
            <div class="kartu p-6">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-teal-soft/70 text-teal">
                    <x-ikon :nama="$kartu[0]" kelas="h-5 w-5" />
                </span>
                <p class="mt-4 text-[11px] font-bold uppercase tracking-[0.12em] text-ink/40">{{ $kartu[1] }}</p>
                <p class="mt-1.5 text-[14px] leading-relaxed text-ink/75">{{ $kartu[2] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="kartu overflow-hidden">
            <iframe
                title="Lokasi Puskesmas Umban Sari"
                class="h-[380px] w-full border-0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.openstreetmap.org/export/embed.html?bbox=101.40%2C0.55%2C101.48%2C0.61&amp;layer=mapnik&amp;marker=0.58%2C101.44"></iframe>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-ink p-6 text-white">
                <h2 class="font-display text-[20px]">Butuh nomor antrian?</h2>
                <p class="mt-2 text-[13px] leading-relaxed text-white/50">
                    Ambil dari mana saja sebelum berangkat, lalu tunjukkan kode tiket di loket.
                </p>
                <a href="{{ route('antrian.form') }}" class="tombol mt-5 w-full bg-cream text-ink hover:bg-white !py-2.5 !text-[13.5px]">
                    <x-ikon nama="ticket" kelas="h-4 w-4" /> Ambil antrian online
                </a>
            </div>

            @if ($whatsapp)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" rel="noopener"
                   class="kartu kartu-naik flex items-center gap-4 p-5">
                    <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#e7f6ec] text-[#1a8f4c]">
                        <x-ikon nama="phone" kelas="h-5 w-5" />
                    </span>
                    <div>
                        <p class="text-[13.5px] font-semibold text-ink">WhatsApp</p>
                        <p class="text-[12.5px] text-ink/50">{{ $whatsapp }}</p>
                    </div>
                </a>
            @endif

            <div class="kartu p-5">
                <p class="text-[13.5px] font-semibold text-ink">Datang langsung</p>
                <p class="mt-1.5 text-[12.5px] leading-relaxed text-ink/55">
                    Pendaftaran di loket dibuka sesuai jam layanan. Bila membawa surat rujukan,
                    serahkan ke petugas saat mendaftar.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
