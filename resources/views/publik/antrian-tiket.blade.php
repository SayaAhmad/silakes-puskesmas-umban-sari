@extends('layouts.publik')
@section('judul', 'Tiket Antrian ' . $antrian->kode)

@section('konten')
<div class="mx-auto max-w-3xl px-5 py-12 sm:px-8">
    <div class="text-center">
        <span class="inline-flex items-center gap-2 rounded-full bg-teal-soft/70 px-3.5 py-1.5 text-[11.5px] font-bold uppercase tracking-[0.12em] text-pine">
            <x-ikon nama="check-bulat" kelas="h-3.5 w-3.5" /> Pendaftaran berhasil
        </span>
        <h1 class="mt-5 text-[30px] leading-tight text-ink sm:text-[36px]">Nomor antrian Anda sudah siap</h1>
        <p class="mt-3 text-[14px] text-ink/55">Simpan halaman ini atau screenshot tiket di bawah.</p>
    </div>

    {{-- Tiket --}}
    <div class="tiket mt-10 overflow-hidden border border-line shadow-[0_30px_60px_-40px_rgba(12,39,35,0.4)]">
        <div class="relative bg-ink px-8 py-9 text-center text-white">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-teal-soft/60">{{ $antrian->poli->nama }}</p>
            <p class="mt-3 font-display text-[76px] font-semibold leading-none tracking-tight">{{ $antrian->kode }}</p>
            <p class="mt-3 text-[13px] text-white/45">{{ $antrian->tanggal->translatedFormat('l, d F Y') }}</p>

            <div class="absolute right-6 top-6">
                <span class="rounded-full px-2.5 py-1 text-[10.5px] font-bold uppercase tracking-wide
                    {{ $antrian->status === 'dipanggil' ? 'bg-coral text-white' : 'bg-white/10 text-white/60' }}">
                    {{ $antrian->status_label }}
                </span>
            </div>
        </div>

        <div class="tiket-garis"></div>

        <div class="grid grid-cols-2 gap-px bg-line sm:grid-cols-4">
            <div class="bg-white px-5 py-5 text-center">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.1em] text-ink/40">Nomor</p>
                <p class="mt-1.5 font-display text-[20px] font-semibold text-ink">{{ str_pad($antrian->nomor, 3, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="bg-white px-5 py-5 text-center">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.1em] text-ink/40">Di depan Anda</p>
                <p class="mt-1.5 font-display text-[20px] font-semibold text-ink" id="sisa-antrian">{{ $didepan }}</p>
            </div>
            <div class="bg-white px-5 py-5 text-center">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.1em] text-ink/40">Estimasi</p>
                <p class="mt-1.5 font-display text-[20px] font-semibold text-ink" id="estimasi">{{ $estimasi ?? '—' }}</p>
            </div>
            <div class="bg-white px-5 py-5 text-center">
                <p class="text-[10.5px] font-bold uppercase tracking-[0.1em] text-ink/40">Sedang dipanggil</p>
                <p class="mt-1.5 font-display text-[20px] font-semibold text-ink" id="sedang">{{ $sedangDipanggil->kode ?? '—' }}</p>
            </div>
        </div>

        <div class="tiket-garis"></div>

        <div class="space-y-3 bg-white px-8 py-7">
            <div class="flex justify-between text-[13.5px]">
                <span class="text-ink/50">Nama pasien</span>
                <span class="font-semibold text-ink">{{ $antrian->nama_pasien }}</span>
            </div>
            <div class="flex justify-between text-[13.5px]">
                <span class="text-ink/50">Jenis kelamin</span>
                <span class="font-medium text-ink">{{ $antrian->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
            </div>
            @if ($antrian->no_hp)
                <div class="flex justify-between text-[13.5px]">
                    <span class="text-ink/50">Nomor HP</span>
                    <span class="font-medium text-ink">{{ $antrian->no_hp }}</span>
                </div>
            @endif
            @if ($antrian->keluhan)
                <div class="flex justify-between gap-6 text-[13.5px]">
                    <span class="shrink-0 text-ink/50">Keluhan</span>
                    <span class="text-right font-medium text-ink">{{ $antrian->keluhan }}</span>
                </div>
            @endif
            <div class="flex justify-between border-t border-dashed border-line pt-3 text-[13.5px]">
                <span class="text-ink/50">Sumber</span>
                <span class="font-medium text-ink">{{ $antrian->sumber === 'online' ? 'Antrian online' : 'Loket' }}</span>
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('antrian.layar') }}" class="tombol tombol-utama flex-1">
            <x-ikon nama="monitor" kelas="h-[18px] w-[18px]" /> Lihat layar panggilan
        </a>
        <button onclick="window.print()" class="tombol tombol-garis flex-1">
            <x-ikon nama="printer" kelas="h-[18px] w-[18px]" /> Cetak / simpan PDF
        </button>
    </div>

    <div class="mt-6 rounded-2xl border border-line bg-sand/40 p-5">
        <p class="flex items-center gap-2 text-[13px] font-semibold text-ink">
            <x-ikon nama="bell" kelas="h-4 w-4 text-teal" /> Langkah berikutnya
        </p>
        <ol class="mt-2.5 space-y-1.5 text-[12.5px] leading-relaxed text-ink/60">
            <li>1. Datang ke puskesmas sesuai estimasi jam di tiket.</li>
            <li>2. Tunjukkan kode <span class="font-semibold text-ink">{{ $antrian->kode }}</span> ke petugas loket.</li>
            <li>3. Tunggu panggilan di ruang tunggu {{ $antrian->poli->nama }}.</li>
        </ol>
    </div>

    <p class="mt-6 text-center text-[12px] text-ink/40">
        Nomor ini tidak dapat dipindahtangankan. Halaman ini diperbarui otomatis.
    </p>
</div>
@endsection

@push('skrip')
<script>
    (function () {
        const kode = @json($antrian->kode);

        async function segarkan() {
            try {
                const res = await fetch(@json(route('antrian.status')), { headers: { Accept: 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();

                const poli = @json($antrian->poli->nama);
                const milikPoli = (data.per_poli || []).find(p => p.poli === poli);
                if (milikPoli) {
                    document.getElementById('sedang').textContent = milikPoli.sekarang || '—';
                }

                if (data.terakhir && data.terakhir.kode === kode) {
                    document.getElementById('sedang').textContent = kode;
                }
            } catch (e) { /* diamkan */ }
        }

        segarkan();
        setInterval(segarkan, 15000);
    })();
</script>
@endpush
