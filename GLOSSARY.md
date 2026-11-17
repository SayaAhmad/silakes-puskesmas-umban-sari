# GLOSSARY — SILAKES

Istilah yang dipakai konsisten di kode, dokumen, dan UI.

| Istilah | Arti |
|---|---|
| **SILAKES** | Sistem Informasi Layanan Kesehatan — nama produk aplikasi Puskesmas Umban Sari. |
| **Poli** | Unit layanan puskesmas (Umum, Gigi, KIA, Anak, Gizi, TB Paru). Tabel `polis`. |
| **Layanan** | Jenis tindakan di dalam poli, punya persyaratan dan durasi. Tabel `layanans`. |
| **Tenaga kesehatan** | Dokter/perawat pemilik jadwal praktik. Tabel `dokters`. Model `Dokter`. |
| **Jadwal** | Slot praktik: poli + dokter + hari (0=Minggu…6=Sabtu) + jam + kuota. Tabel `jadwals`. |
| **Antrian** | Satu pendaftaran kunjungan pada tanggal tertentu. Tabel `antrians`. |
| **Kode antrian** | Huruf poli + nomor 3 digit, mis. `U-001`, `G-002`. Unik per poli per hari. |
| **Huruf poli** | `U`mum, `G`igi, `K`IA, `A`nak, Gi`z`i, `T`B Paru — dijaga unik agar kode tidak bentrok. |
| **Status antrian** | `menunggu` → `dipanggil` → `selesai`; cabang `lewat` dan `batal`. |
| **Sumber** | Asal antrian: `online` (dari situs) atau `loket` (didaftarkan petugas). |
| **Layar panggilan** | Halaman `/antrian/layar` untuk ruang tunggu, auto-refresh dari `/antrian/status`. |
| **Panel** | Area pengelola di `/panel`, butuh login. |
| **Petugas / Admin** | Peran pengguna. `admin` boleh kelola pengguna & log aktivitas; `petugas` tidak. |
| **Pengaturan** | Tabel key-value `pengaturans` untuk identitas puskesmas, jam buka, kontak. |
| **Estimasi** | Perkiraan jam dilayani = jam panggilan terakhir + (selisih nomor × durasi layanan). |
| **Userland install** | Pemasangan PHP/MariaDB di `~/.local` tanpa root (sudo VPS tidak tersedia). |
