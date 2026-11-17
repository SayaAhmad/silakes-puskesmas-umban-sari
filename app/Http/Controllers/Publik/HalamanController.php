<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Jadwal;
use App\Models\Layanan;
use App\Models\Pengaturan;
use App\Models\Pengumuman;
use App\Models\Poli;

class HalamanController extends Controller
{
    public function layanan()
    {
        return view('publik.layanan', [
            'polis' => Poli::aktif()->urut()->with(['layanans' => fn ($q) => $q->where('is_active', true)->orderBy('urutan')])->get(),
            'semuaLayanan' => Layanan::with('poli')->aktif()->orderBy('urutan')->get(),
        ]);
    }

    public function layananDetail(Layanan $layanan)
    {
        abort_unless($layanan->is_active, 404);

        return view('publik.layanan-detail', [
            'layanan' => $layanan->load('poli'),
            'lainnya' => Layanan::aktif()
                ->where('id', '!=', $layanan->id)
                ->where('poli_id', $layanan->poli_id)
                ->limit(3)
                ->get(),
        ]);
    }

    public function jadwal()
    {
        $jadwal = Jadwal::with(['dokter', 'poli'])
            ->aktif()
            ->get()
            ->sortBy(fn ($j) => [$j->poli->urutan ?? 99, $j->hari, $j->jam_mulai])
            ->groupBy('hari');

        return view('publik.jadwal', [
            'hari' => Jadwal::HARI,
            'jadwal' => $jadwal,
            'hariIni' => now()->dayOfWeek,
            'polis' => Poli::aktif()->urut()->get(),
        ]);
    }

    public function alur()
    {
        return view('publik.alur', [
            'langkah' => [
                ['judul' => 'Ambil nomor antrian', 'isi' => 'Ambil dari website ini sebelum berangkat, atau langsung di mesin antrian/loket saat tiba. Nomor online lebih dulu dipanggil sesuai urutan.', 'butuh' => null, 'ikon' => 'ticket'],
                ['judul' => 'Datang & daftar di loket', 'isi' => 'Tunjukkan kode tiket atau sebutkan nama. Petugas memverifikasi data dan mengarahkan ke poli tujuan.', 'butuh' => 'KTP / Kartu Keluarga, kartu BPJS (bila ada)', 'ikon' => 'clipboard'],
                ['judul' => 'Tunggu panggilan', 'isi' => 'Nomor Anda tampil di layar ruang tunggu. Estimasi waktu terlihat di tiket Anda.', 'butuh' => null, 'ikon' => 'hourglass'],
                ['judul' => 'Pemeriksaan di poli', 'isi' => 'Tenaga kesehatan memeriksa sesuai keluhan. Untuk layanan tertentu, bawa surat rujukan.', 'butuh' => 'Surat rujukan (layanan tertentu)', 'ikon' => 'stethoscope'],
                ['judul' => 'Farmasi / tindakan', 'isi' => 'Obat diambil di depo farmasi setelah resep diproses. Ikuti arahan petugas untuk tindakan lanjutan.', 'butuh' => 'Resep dari poli', 'ikon' => 'pill'],
                ['judul' => 'Selesai / rujukan', 'isi' => 'Bila perlu rujukan ke fasilitas lanjutan, petugas menyiapkan surat dan berkas yang dibutuhkan.', 'butuh' => null, 'ikon' => 'check'],
            ],
        ]);
    }

    public function pengumuman()
    {
        return view('publik.pengumuman', [
            'pengumuman' => Pengumuman::terbit()->latest('published_at')->paginate(6),
        ]);
    }

    public function pengumumanDetail(Pengumuman $pengumuman)
    {
        abort_unless($pengumuman->is_published, 404);

        return view('publik.pengumuman-detail', [
            'pengumuman' => $pengumuman->load('user'),
            'lainnya' => Pengumuman::terbit()
                ->where('id', '!=', $pengumuman->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }

    public function faq()
    {
        $faq = Faq::aktif()->get()->groupBy('kategori');

        return view('publik.faq', [
            'faq' => $faq,
            'labelKategori' => Faq::KATEGORI,
        ]);
    }

    public function kontak()
    {
        return view('publik.kontak', [
            'alamat' => Pengaturan::get('alamat', 'Jl. Umban Sari, Rumbai, Pekanbaru, Riau'),
            'telepon' => Pengaturan::get('telepon', '(0761) 000000'),
            'whatsapp' => Pengaturan::get('whatsapp', ''),
            'email' => Pengaturan::get('email', 'puskesmas.umbansari@pku.go.id'),
            'jamOperasional' => Pengaturan::get('jam_operasional', 'Senin–Sabtu, 08.00–14.00'),
        ]);
    }
}
