@extends('layouts.publik')
@section('judul', 'Layar Panggilan')

@section('konten')
<div class="min-h-[calc(100vh-72px)] bg-ink text-white" x-data="layarAntrian()" x-init="mulai()">
    <div class="mx-auto max-w-[1600px] px-6 py-8 sm:px-10">

        {{-- Kepala --}}
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-6">
            <div class="flex items-center gap-4">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-teal">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                        <path d="M12 4v16M4 12h16"/>
                    </svg>
                </span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-teal-soft/50">Layar panggilan</p>
                    <p class="font-display text-[20px] leading-tight">Puskesmas Umban Sari</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="text-right">
                    <p class="font-display text-[26px] font-semibold leading-none tabular-nums" x-text="waktu">--:--:--</p>
                    <p class="mt-1 text-[11.5px] text-white/35" x-text="tanggal"></p>
                </div>
                <span class="flex items-center gap-2 rounded-full bg-white/8 px-3 py-1.5 text-[11px] font-semibold text-teal-soft/70">
                    <span class="dot-hidup"></span> Live
                </span>
            </div>
        </div>

        <div class="mt-8 grid gap-6 lg:grid-cols-[1.25fr_1fr]">

            {{-- Panggilan utama --}}
            <div class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-[#12433b] to-[#0a211d] p-9 ring-1 ring-white/8">
                <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-teal/20 blur-3xl"></div>

                <p class="text-[12px] font-bold uppercase tracking-[0.2em] text-teal-soft/60">Nomor dipanggil</p>
                <div class="mt-6 flex items-end gap-6">
                    <p class="font-display text-[clamp(64px,11vw,150px)] font-semibold leading-[0.85] tracking-[-0.04em]"
                       x-text="terakhir ? terakhir.kode : '—'"></p>
                </div>

                <p class="mt-6 text-[clamp(18px,2.4vw,30px)] font-medium text-white/85" x-text="terakhir ? terakhir.poli : 'Menunggu panggilan'"></p>
                <p class="mt-1.5 text-[clamp(13px,1.4vw,17px)] text-white/35" x-text="terakhir ? ('a.n. ' + terakhir.nama) : ''"></p>

                <div class="mt-8 flex flex-wrap items-center gap-3 border-t border-white/10 pt-6">
                    <span class="text-[12px] text-white/35">Dipanggil pukul</span>
                    <span class="font-display text-[16px] font-semibold" x-text="terakhir ? (terakhir.dipanggil_at || '—') : '—'"></span>
                </div>
            </div>

            {{-- Status per poli --}}
            <div class="space-y-3">
                <template x-for="p in perPoli" :key="p.poli">
                    <div class="flex items-center gap-4 rounded-2xl bg-white/[0.045] px-5 py-4 ring-1 ring-inset ring-white/[0.07]">
                        <span class="h-2 w-2 shrink-0 rounded-full" :style="`background:${p.warna}`"></span>
                        <span class="flex-1 truncate text-[14px] font-medium text-white/70" x-text="p.poli"></span>
                        <span class="font-display text-[22px] font-semibold"
                              :class="p.sekarang ? 'text-white' : 'text-white/30'"
                              x-text="p.sekarang || p.berikutnya || '—'"></span>
                        <span class="w-16 text-right text-[11.5px] text-white/30" x-text="p.menunggu + ' tunggu'"></span>
                    </div>
                </template>

                <div x-show="perPoli.length === 0" class="rounded-2xl bg-white/[0.03] px-5 py-8 text-center text-[13px] text-white/30">
                    Belum ada data poli.
                </div>
            </div>
        </div>

        {{-- Daftar menunggu --}}
        <div class="mt-6 rounded-[24px] bg-white/[0.035] p-6 ring-1 ring-inset ring-white/[0.06]">
            <div class="flex items-center justify-between">
                <p class="text-[12px] font-bold uppercase tracking-[0.16em] text-teal-soft/50">Menunggu berikutnya</p>
                <p class="text-[11.5px] text-white/30"><span x-text="menunggu.length"></span> antrian</p>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-6">
                <template x-for="(m, i) in menunggu.slice(0, 12)" :key="m.kode">
                    <div class="rounded-xl bg-white/[0.05] px-3 py-2.5 text-center">
                        <p class="font-display text-[18px] font-semibold" x-text="m.kode"></p>
                        <p class="mt-0.5 truncate text-[10.5px] text-white/30" x-text="m.poli"></p>
                    </div>
                </template>
                <div x-show="menunggu.length === 0" class="col-span-full py-6 text-center text-[13px] text-white/25">
                    Tidak ada antrian menunggu.
                </div>
            </div>
        </div>

        <p class="mt-6 text-center text-[11.5px] text-white/20">
            Layar diperbarui otomatis setiap 5 detik · Tekan F11 untuk mode layar penuh
        </p>
    </div>
</div>
@endsection

@push('skrip')
<script>
    function layarAntrian() {
        return {
            waktu: '--:--:--',
            tanggal: '',
            terakhir: null,
            menunggu: [],
            perPoli: [],
            sebelumnya: null,
            mulai() {
                this.ambil();
                setInterval(() => this.ambil(), 5000);
                setInterval(() => this.jam(), 1000);
                this.jam();
            },
            jam() {
                const d = new Date();
                this.waktu = d.toLocaleTimeString('id-ID', { hour12: false });
            },
            async ambil() {
                try {
                    const res = await fetch(@json(route('antrian.status')), { headers: { Accept: 'application/json' } });
                    const data = await res.json();

                    this.tanggal = data.tanggal;
                    this.menunggu = data.menunggu || [];
                    this.perPoli = data.per_poli || [];

                    const kodeBaru = data.terakhir?.kode ?? null;
                    if (kodeBaru && kodeBaru !== this.sebelumnya) {
                        this.sebelumnya = kodeBaru;
                        this.umumkan(data.terakhir);
                    }
                    this.terakhir = data.terakhir;
                } catch (e) { /* diamkan, coba lagi */ }
            },
            umumkan(t) {
                if (!('speechSynthesis' in window) || !t) return;
                const teks = `Nomor antrian ${t.kode.split('').join(' ')}, silakan menuju ${t.poli}`;
                const ucap = new SpeechSynthesisUtterance(teks);
                ucap.lang = 'id-ID';
                ucap.rate = 0.9;
                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(ucap);
            }
        };
    }
</script>
@endpush
