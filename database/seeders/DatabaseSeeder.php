<?php

namespace Database\Seeders;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Faq;
use App\Models\Jadwal;
use App\Models\Layanan;
use App\Models\Pengaturan;
use App\Models\Pengumuman;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Pengguna panel ---
        $admin = User::create([
            'name' => 'Ahmad Asrofiq',
            'email' => 'admin@silakes.test',
            'password' => 'admin12345',
            'role' => 'admin',
            'jabatan' => 'Administrator sistem',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Rina Marlina',
            'email' => 'petugas@silakes.test',
            'password' => 'petugas12345',
            'role' => 'petugas',
            'jabatan' => 'Petugas pendaftaran',
            'is_active' => true,
        ]);

        // --- Poli ---
        $poliData = [
            ['nama' => 'Poli Umum', 'ikon' => 'stethoscope', 'warna' => '#12796a', 'urutan' => 1,
             'deskripsi' => 'Pelayanan pemeriksaan kesehatan umum untuk semua usia, menjadi pintu masuk pertama layanan puskesmas.'],
            ['nama' => 'Poli Gigi', 'ikon' => 'tooth', 'warna' => '#3b7ea1', 'urutan' => 2,
             'deskripsi' => 'Pemeriksaan, perawatan, dan penyuluhan kesehatan gigi dan mulut.'],
            ['nama' => 'Poli KIA', 'ikon' => 'heart', 'warna' => '#c2543f', 'urutan' => 3,
             'deskripsi' => 'Kesehatan ibu dan anak: pemeriksaan kehamilan, nifas, dan tumbuh kembang balita.'],
            ['nama' => 'Poli Anak', 'ikon' => 'baby', 'warna' => '#8a6bb1', 'urutan' => 4,
             'deskripsi' => 'Pelayanan kesehatan anak, imunisasi, dan penanganan penyakit anak.'],
            ['nama' => 'Poli Gizi', 'ikon' => 'leaf', 'warna' => '#4a8a5c', 'urutan' => 5,
             'deskripsi' => 'Konsultasi gizi, penanganan gizi kurang, dan edukasi pola makan sehat.'],
            ['nama' => 'Poli TB Paru', 'ikon' => 'shield', 'warna' => '#b8860b', 'urutan' => 6,
             'deskripsi' => 'Skrining, pengobatan, dan pemantauan tuberkulosis.'],
        ];

        $polis = collect($poliData)->map(fn ($p) => Poli::create($p + ['is_active' => true]));

        // --- Layanan ---
        $layananData = [
            [0, 'Pemeriksaan Umum', 'Pemeriksaan dokter untuk keluhan umum seperti demam, batuk, dan nyeri.', 15, false,
             ['KTP atau Kartu Keluarga', 'Kartu BPJS/KIS (bila ada)']],
            [0, 'Surat Keterangan Sehat', 'Pemeriksaan singkat untuk keperluan surat keterangan sehat.', 20, false,
             ['KTP', 'Pas foto 3x4 (1 lembar)']],
            [1, 'Pembersihan Karang Gigi', 'Scaling untuk membersihkan karang dan plak gigi.', 30, false,
             ['KTP atau Kartu Keluarga', 'Kartu BPJS/KIS']],
            [1, 'Penambalan Gigi', 'Penambalan gigi berlubang dengan bahan komposit.', 40, false,
             ['KTP atau Kartu Keluarga', 'Kartu BPJS/KIS']],
            [2, 'Pemeriksaan Kehamilan (ANC)', 'Pemeriksaan rutin ibu hamil, termasuk pemeriksaan tekanan darah dan lingkar perut.', 20, false,
             ['Buku KIA', 'KTP', 'Kartu BPJS/KIS']],
            [2, 'Keluarga Berencana', 'Konsultasi dan pelayanan alat kontrasepsi.', 15, false,
             ['KTP suami/istri', 'Kartu BPJS/KIS']],
            [3, 'Pemeriksaan Anak Sakit', 'Pemeriksaan dan pengobatan anak dengan keluhan akut.', 15, false,
             ['Kartu Keluarga', 'Buku KIA', 'Kartu BPJS/KIS']],
            [3, 'Imunisasi Dasar', 'Imunisasi sesuai jadwal usia anak.', 10, false,
             ['Buku KIA', 'Kartu Keluarga']],
            [4, 'Konsultasi Gizi', 'Konsultasi pola makan dan penanganan masalah gizi.', 25, false,
             ['KTP atau Kartu Keluarga', 'Hasil pemeriksaan laboratorium (bila ada)']],
            [5, 'Pemeriksaan Dahak TB', 'Pemeriksaan dahak untuk suspek tuberkulosis.', 15, false,
             ['KTP', 'Surat rujukan fasyankes (bila dirujuk)']],
            [5, 'Pengobatan TB', 'Pengambilan obat dan pemantauan kepatuhan minum obat.', 10, true,
             ['KTP', 'Kartu berobat TB', 'Surat rujukan fasyankes']],
        ];

        foreach ($layananData as $i => [$poliIdx, $nama, $ringkasan, $durasi, $rujukan, $syarat]) {
            Layanan::create([
                'poli_id' => $polis[$poliIdx]->id,
                'nama' => $nama,
                'ringkasan' => $ringkasan,
                'deskripsi' => $ringkasan . ' Layanan diberikan oleh tenaga kesehatan puskesmas sesuai standar prosedur pelayanan.',
                'persyaratan' => $syarat,
                'durasi_menit' => $durasi,
                'butuh_rujukan' => $rujukan,
                'is_active' => true,
                'urutan' => $i + 1,
            ]);
        }

        // --- Tenaga kesehatan ---
        $dokterData = [
            [0, 'dr. Siti Rahmawati', 'M.Ked', 'Poli Umum', 'Dokter umum dengan fokus pelayanan kesehatan keluarga dan penyakit tidak menular.'],
            [0, 'dr. Bagas Prakoso', null, 'Poli Umum', 'Dokter umum, menangani keluhan akut dan rujukan lanjutan.'],
            [1, 'drg. Nadia Puspita', null, 'Poli Gigi', 'Dokter gigi, fokus pada perawatan preventif dan penambalan.'],
            [2, 'dr. Lestari Wulandari', 'Sp.OG', 'Poli KIA', 'Dokter spesialis obstetri dan ginekologi untuk layanan ibu dan anak.'],
            [3, 'dr. Fajar Nugroho', 'Sp.A', 'Poli Anak', 'Dokter spesialis anak, menangani tumbuh kembang dan penyakit anak.'],
            [4, 'Ns. Dewi Anggraini', 'S.Kep', 'Poli Gizi', 'Perawat sekaligus konselor gizi komunitas.'],
            [5, 'dr. Hendra Saputra', null, 'Poli TB Paru', 'Dokter penanggung jawab program tuberkulosis puskesmas.'],
        ];

        $dokters = collect($dokterData)->map(fn ($d, $i) => Dokter::create([
            'poli_id' => $polis[$d[0]]->id,
            'nama' => $d[1],
            'gelar' => $d[2],
            'sip' => str_pad((string) (503 + $i), 4, '0', STR_PAD_LEFT) . '/DINKES/2026',
            'bio' => $d[4],
            'is_active' => true,
            'urutan' => $i + 1,
        ]));

        // --- Jadwal praktik (0 = Minggu ... 6 = Sabtu) ---
        // Tiap poli praktik minimal Senin–Jumat agar contoh antrian "hari ini" konsisten.
        $jadwalData = [
            [0, [1, 2, 3, 4, 5, 6], '08:00', '14:00', 40],
            [1, [1, 3, 5], '08:00', '13:00', 30],
            [2, [1, 2, 3, 4, 5], '09:00', '12:00', 15],
            [3, [2, 4, 6], '08:00', '12:00', 25],
            [4, [1, 2, 3, 5], '08:00', '11:00', 20],
            [5, [2, 4], '08:00', '11:00', 15],
            [6, [1, 2, 3, 4, 5], '08:00', '12:00', 20],
        ];

        foreach ($jadwalData as [$dokterIdx, $hariList, $mulai, $selesai, $kuota]) {
            foreach ($hariList as $hari) {
                Jadwal::create([
                    'dokter_id' => $dokters[$dokterIdx]->id,
                    'poli_id' => $dokters[$dokterIdx]->poli_id,
                    'hari' => $hari,
                    'jam_mulai' => $mulai,
                    'jam_selesai' => $selesai,
                    'kuota' => $kuota,
                    'is_active' => true,
                ]);
            }
        }

        // --- Antrian hari ini (contoh) ---
        $contoh = [
            [0, 'Andi Saputra', 'L', 'dipanggil', 'Demam sejak dua hari'],
            [0, 'Maya Sari', 'P', 'menunggu', 'Batuk dan pilek'],
            [0, 'Rudi Hartono', 'L', 'menunggu', 'Kontrol tekanan darah'],
            [0, 'Yuni Astuti', 'P', 'selesai', 'Nyeri ulu hati'],
            [1, 'Doni Pratama', 'L', 'dipanggil', 'Gigi berlubang'],
            [1, 'Sri Wahyuni', 'P', 'menunggu', 'Karang gigi'],
            [2, 'Ratna Dewi', 'P', 'menunggu', 'Pemeriksaan kehamilan'],
            [2, 'Ika Purnama', 'P', 'selesai', 'Konsultasi KB'],
            [3, 'Bima Aditya', 'L', 'menunggu', 'Demam dan ruam'],
            [4, 'Hasan Basri', 'L', 'menunggu', 'Konsultasi gizi'],
            [5, 'Joko Susilo', 'L', 'menunggu', 'Kontrol obat TB'],
        ];

        $nomor = [];
        foreach ($contoh as $i => [$poliIdx, $nama, $jk, $status, $keluhan]) {
            $poli = $polis[$poliIdx];
            $nomor[$poliIdx] = ($nomor[$poliIdx] ?? 0) + 1;

            $dipanggil = in_array($status, ['dipanggil', 'selesai'], true) ? now()->subMinutes(30 - $i) : null;
            $dibuat = $dipanggil ? $dipanggil->copy()->subMinutes(15 + $i) : now()->subMinutes(20 + $i);

            $antrian = new Antrian([
                'tanggal' => now()->toDateString(),
                'poli_id' => $poli->id,
                'nomor' => $nomor[$poliIdx],
                'kode' => Antrian::generateKodeUnik($poli, $nomor[$poliIdx]),
                'nama_pasien' => $nama,
                'nik' => '1471' . str_pad((string) (100000000000 + $i * 7919), 12, '0', STR_PAD_LEFT),
                'no_hp' => '0812' . str_pad((string) (3000000 + $i * 137), 7, '0', STR_PAD_LEFT),
                'jenis_kelamin' => $jk,
                'keluhan' => $keluhan,
                'status' => $status,
                'sumber' => $i % 4 === 0 ? 'loket' : 'online',
                'dipanggil_at' => $dipanggil,
                'selesai_at' => $status === 'selesai' ? now()->subMinutes(10) : null,
            ]);
            // created_at tidak mass-assignable: pasien mendaftar lebih dulu daripada dipanggil.
            $antrian->created_at = $dibuat;
            $antrian->save();
        }

        // --- Pengumuman ---
        Pengumuman::create([
            'judul' => 'Layanan Antrian Online Kini Tersedia',
            'kategori' => 'layanan',
            'ringkasan' => 'Ambil nomor antrian dari rumah, datang sesuai estimasi waktu yang tertera di tiket.',
            'isi' => "Puskesmas Umban Sari kini menyediakan layanan antrian online. Masyarakat dapat mengambil nomor antrian melalui situs ini tanpa perlu datang lebih awal untuk mengantre.\n\nSetelah mengisi formulir, simpan kode antrian yang muncul. Kode tersebut dapat ditunjukkan kepada petugas pendaftaran saat tiba di puskesmas.\n\nLayanan ini bersifat tambahan. Pasien yang datang langsung tetap dilayani melalui loket pendaftaran.",
            'is_published' => true,
            'published_at' => now()->subDays(2),
            'user_id' => $admin->id,
        ]);

        Pengumuman::create([
            'judul' => 'Perubahan Jam Layanan Selama Libur Nasional',
            'kategori' => 'jadwal',
            'ringkasan' => 'Pada hari libur nasional, layanan puskesmas hanya menerima kasus darurat.',
            'isi' => "Sehubungan dengan hari libur nasional, layanan puskesmas pada tanggal tersebut hanya melayani kasus darurat dan persalinan.\n\nLayanan administrasi dan poli rawat jalan kembali normal pada hari kerja berikutnya. Mohon menyesuaikan jadwal kunjungan Anda.",
            'is_published' => true,
            'published_at' => now()->subDays(6),
            'user_id' => $admin->id,
        ]);

        Pengumuman::create([
            'judul' => 'Pemeriksaan Kesehatan Gratis untuk Lansia',
            'kategori' => 'kesehatan',
            'ringkasan' => 'Pemeriksaan tekanan darah, gula darah, dan kolesterol tanpa biaya setiap Jumat pagi.',
            'isi' => "Dalam rangka meningkatkan kesehatan lanjut usia, puskesmas membuka layanan pemeriksaan kesehatan gratis setiap Jumat pagi.\n\nPemeriksaan meliputi tekanan darah, gula darah sewaktu, dan kolesterol. Peserta cukup membawa KTP dan kartu berobat.\n\nKuota terbatas dan dilayani sesuai urutan kedatangan.",
            'is_published' => true,
            'published_at' => now()->subDays(12),
            'user_id' => $admin->id,
        ]);

        // --- FAQ ---
        $faqData = [
            ['Apakah harus mendaftar online untuk berobat?', 'Tidak. Pendaftaran online bersifat pilihan untuk mempersingkat waktu tunggu. Pasien tetap dapat mendaftar langsung di loket saat datang.', 'pendaftaran', 1],
            ['Berapa lama estimasi waktu tunggu setelah mengambil antrian online?', 'Estimasi tertera pada tiket dan dihitung dari jam mulai praktik poli ditambah durasi layanan. Waktu dapat bergeser bila ada kasus yang memerlukan penanganan lebih lama.', 'pendaftaran', 2],
            ['Apakah antrian online bisa dibatalkan?', 'Bisa. Hubungi petugas pendaftaran dengan menyebutkan kode antrian Anda, atau gunakan menu pembatalan pada halaman tiket.', 'pendaftaran', 3],
            ['Apakah layanan puskesmas menerima BPJS?', 'Ya. Puskesmas menerima pasien peserta BPJS/KIS sesuai prosedur. Bawa kartu identitas dan kartu peserta saat berkunjung.', 'biaya', 4],
            ['Apakah ada biaya untuk surat keterangan sehat?', 'Surat keterangan sehat mengikuti tarif retribusi daerah yang berlaku. Rincian biaya dapat ditanyakan di loket pendaftaran.', 'biaya', 5],
            ['Apa saja yang perlu dibawa saat pemeriksaan kehamilan?', 'Buku KIA, KTP, dan kartu BPJS/KIS. Bila ada hasil pemeriksaan sebelumnya, bawa juga untuk membantu penilaian.', 'layanan', 6],
            ['Apakah bisa berobat tanpa kartu identitas?', 'Pasien tetap dilayani untuk kasus darurat. Untuk layanan rutin, identitas diperlukan guna pencatatan kunjungan.', 'umum', 7],
            ['Bagaimana cara merujuk ke rumah sakit?', 'Rujukan diberikan dokter setelah pemeriksaan bila kondisi memerlukan layanan lanjutan. Proses rujukan mengikuti ketentuan BPJS dan ketersediaan fasilitas tujuan.', 'layanan', 8],
        ];

        foreach ($faqData as [$tanya, $jawab, $kategori, $urut]) {
            Faq::create([
                'pertanyaan' => $tanya,
                'jawaban' => $jawab,
                'kategori' => $kategori,
                'urutan' => $urut,
                'is_active' => true,
            ]);
        }

        // --- Pengaturan situs ---
        $pengaturan = [
            ['nama_puskesmas', 'Puskesmas Umban Sari', 'umum'],
            ['alamat', 'Jl. Umban Sari, Kecamatan Rumbai, Kota Pekanbaru, Riau', 'umum'],
            ['telepon', '(0761) 123456', 'umum'],
            ['whatsapp', '0812-3456-7890', 'umum'],
            ['email', 'puskesmas.umbansari@pku.go.id', 'umum'],
            ['jam_operasional', 'Senin–Jumat 08.00–14.00 WIB, Sabtu 08.00–11.00 WIB', 'operasional'],
            ['jam_buka', '08:00', 'operasional'],
            ['jam_tutup', '14:00', 'operasional'],
            ['hari_kerja', 'Senin–Jumat', 'operasional'],
            ['kepala_nama', 'dr. Hj. Marlina Yusuf, M.Kes', 'umum'],
            ['kepala_nip', '19780512 200604 2 003', 'umum'],
            ['visi', 'Terwujudnya masyarakat Kecamatan Rumbai yang sehat, mandiri, dan berkeadilan dalam pelayanan kesehatan.', 'umum'],
            ['misi', "Meningkatkan akses dan mutu pelayanan kesehatan dasar.\nMenguatkan upaya promotif dan preventif berbasis masyarakat.\nMengembangkan tata kelola puskesmas yang transparan dan akuntabel.", 'umum'],
            ['sambutan', 'Selamat datang di situs resmi Puskesmas Umban Sari. Kami berkomitmen memberikan pelayanan kesehatan dasar yang mudah diakses, ramah, dan bermutu bagi seluruh masyarakat Kecamatan Rumbai.', 'umum'],
            ['retensi_hari', '90', 'privasi'],
        ];

        foreach ($pengaturan as [$key, $value, $grup]) {
            Pengaturan::set($key, $value, $grup);
        }
    }
}
