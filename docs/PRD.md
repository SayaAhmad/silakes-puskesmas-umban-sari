# PRD — SILAKES: Sistem Informasi Layanan Kesehatan Puskesmas Umban Sari

**Versi** 1.0 · **Tanggal** 17 November 2026 · **Basis** Laporan Capstone Project Kelompok 4
**Stack** Laravel 12 · MySQL (MariaDB 11.4) · Blade + Tailwind CSS 4 · Alpine.js

---

## 1. Ringkasan Produk

SILAKES adalah sistem informasi layanan kesehatan berbasis website untuk Puskesmas Umban Sari, Pekanbaru. Produk ini terdiri dari dua sisi:

- **Situs publik** — masyarakat menemukan jenis layanan, jadwal dokter, persyaratan, alur berkunjung, dan **mengambil nomor antrian online** sebelum datang.
- **Panel pengelola** — petugas berwenang memperbarui seluruh konten dan mengelola antrian harian.

Yang membedakan dari "website profil puskesmas biasa": **antrian online + layar panggilan real-time**. Informasi bukan hanya dibaca, tapi langsung menghasilkan tindakan (nomor antrian) dan mengurangi pertanyaan berulang di loket.

**Batas produk** (sesuai laporan): sistem tidak menegakkan diagnosis, tidak menggantikan konsultasi tenaga kesehatan, dan tidak menyimpan rekam medis elektronik. Sistem hanya mengelola informasi layanan dan administrasi kunjungan.

---

## 2. Pengguna & Peran

| Peran | Deskripsi | Hak akses |
|---|---|---|
| **Masyarakat** (anonim) | Warga yang mencari info layanan / ambil antrian | Seluruh halaman publik, ambil nomor antrian, cek status antrian |
| **Petugas** | Staf loket / administrasi | Panel pengelola: kelola antrian, pengumuman, FAQ |
| **Admin** | Pengelola sistem | Semua di atas + kelola poli, layanan, dokter, jadwal, pengguna, pengaturan, log aktivitas |

Login hanya untuk petugas & admin. Masyarakat tidak perlu akun — hambatan masuk nol, sesuai konteks puskesmas.

---

## 3. Masalah → Solusi

| Masalah (laporan) | Solusi di produk |
|---|---|
| Info jadwal/persyaratan/alur tidak mudah ditemukan | Halaman terstruktur + pencarian instan lintas layanan |
| Masyarakat datang hanya untuk bertanya | Info lengkap per layanan + FAQ + alur bergambar |
| Pertanyaan berulang membebani petugas | Konten terpusat, satu sumber kebenaran, bisa diperbarui sendiri |
| Info berubah tapi media cetak statis | Panel pengelola: ubah jadwal/persyaratan langsung tayang |
| Menumpuk di loket pagi hari | Antrian online: nomor diambil dari rumah, estimasi waktu tunggu |
| Petugas harus meneriakkan nomor | Layar antrian publik + panggilan dari panel |

---

## 4. Fitur

### 4.1 Situs Publik

**F-01 Beranda**
- Status buka/tutup otomatis dari jam operasional & jadwal hari ini.
- Antrian berjalan (nomor sedang dilayani per poli), diperbarui otomatis tiap 10 detik.
- Layanan unggulan, jadwal dokter hari ini, pengumuman terbaru, FAQ singkat.
- Tombol utama: **Ambil Antrian** dan **Lihat Layanan**.

**F-02 Layanan**
- Daftar semua layanan, dikelompokkan per poli, dengan pencarian instan (judul/ringkasan/poli) tanpa reload.
- Detail layanan: deskripsi, **persyaratan** (daftar), durasi perkiraan, poli penanggung jawab, apakah butuh rujukan, alur singkat, tombol ambil antrian untuk poli terkait.

**F-03 Jadwal Dokter**
- Tabel jadwal per poli dan per hari (Senin–Minggu), jam mulai–selesai, kuota.
- Penanda "Hari ini" dan "Sedang berlangsung" dihitung dari waktu server.

**F-04 Alur Pendaftaran**
- Langkah bernomor: datang → ambil nomor → tunggu panggilan → verifikasi berkas → pelayanan → farmasi/rujukan.
- Tiap langkah bisa ditandai butuh berkas apa.

**F-05 Antrian Online**
- Form: poli, nama, NIK (16 digit), no. HP, jenis kelamin, keluhan singkat.
- Sistem menerbitkan **kode tiket** (mis. `A-014`) + nomor urut + estimasi jam dilayani.
- Halaman tiket menampilkan status (menunggu/dipanggil/selesai/lewat) yang bisa di-refresh.
- Satu NIK hanya boleh punya satu antrian aktif per hari per poli (anti-dobel).
- Antrian tutup otomatis di luar jam operasional.

**F-06 Layar Antrian**
- Halaman layar penuh untuk TV ruang tunggu: nomor sedang dipanggil per poli, nomor berikutnya, jam, running text pengumuman. Auto-refresh.

**F-07 Pengumuman & FAQ**
- Daftar + detail pengumuman (kategori, tanggal, penulis).
- FAQ akordeon, dikelompokkan.

**F-08 Kontak**
- Alamat, telepon, jam operasional, peta (embed OpenStreetMap), tautan WhatsApp.

### 4.2 Panel Pengelola

**F-09 Login & Keamanan**
- Login email + password, rate limit 5 percobaan/menit, remember me, logout.
- Akun nonaktif tidak bisa masuk. Middleware peran untuk memisahkan hak akses.
- Ganti password sendiri.

**F-10 Dasbor**
- KPI: antrian hari ini, sedang menunggu, selesai, rata-rata waktu tunggu.
- Grafik 7 hari terakhir (tanpa library chart eksternal — SVG/CSS).
- Komposisi per poli, pengumuman terbaru, aktivitas terakhir.

**F-11 Manajemen Antrian**
- Daftar antrian per tanggal & poli, filter status.
- Aksi: **Panggil** (menunggu→dipanggil), **Selesai**, **Lewati**, **Batal**.
- Panggil-berikutnya satu klik. Nomor yang dipanggil tampil di layar publik.
- Tambah antrian manual untuk pasien walk-in di loket.

**F-12 Konten (CRUD)**
- Poli, Layanan, Dokter, Jadwal, Pengumuman, FAQ — semua bisa dibuat/diubah/dihapus/diaktifkan.
- Pengaturan situs: nama, alamat, telepon, jam operasional per hari, running text, kuota antrian.

**F-13 Pengguna** (admin saja)
- CRUD akun petugas/admin, aktif/nonaktif, reset password.

**F-14 Log Aktivitas**
- Catatan siapa mengubah apa dan kapan, untuk audit internal.

---

## 5. Alur Utama

**Masyarakat:** buka situs → cek jadwal layanan → ambil antrian → simpan kode tiket → datang sesuai estimasi → pantau layar/HP → dipanggil.

**Petugas:** login → buka manajemen antrian → panggil berikutnya → tandai selesai → perbarui pengumuman bila perlu.

**Admin:** login → ubah jadwal dokter / persyaratan layanan → simpan → langsung tayang di situs publik.

---

## 6. Model Data

`users` (role, is_active) · `polis` · `layanans` (poli_id, persyaratan) · `dokters` (poli_id) · `jadwals` (dokter_id, poli_id, hari, jam) · `antrians` (tanggal, poli_id, nomor, kode, status) · `pengumumans` · `faqs` · `pengaturans` (key/value) · `activity_logs`

Aturan penting:
- Nomor antrian unik per (tanggal, poli).
- Jadwal disimpan per hari (0=Minggu … 6=Sabtu) agar mudah dicek "hari ini".
- Pengaturan situs disimpan sebagai key/value agar bisa diubah tanpa deploy.

---

## 7. Non-Fungsional

- **Responsif** — mobile-first; mayoritas warga mengakses dari HP.
- **Cepat** — tanpa build asset eksternal berat; halaman publik < 200 KB.
- **Aksesibel** — kontras WCAG AA, target sentuh ≥ 44 px, label form eksplisit, navigasi keyboard.
- **Aman** — CSRF semua form, validasi server-side, escaping Blade, rate limit login, password ter-hash bcrypt.
- **Tanpa data medis** — tidak ada diagnosis/rekam medis, sesuai batas laporan.
- **Bahasa** — seluruh antarmuka Bahasa Indonesia, format tanggal Indonesia.

---

## 8. Di Luar Cakupan (v1)

Rekam medis elektronik, resep digital, pembayaran/BPJS, notifikasi WhatsApp/SMS otomatis, aplikasi mobile native, telekonsultasi, integrasi P-Care/Kemenkes.

---

## 9. Kriteria Selesai

1. Semua halaman publik dapat diakses tanpa login dan menampilkan data dari database (bukan hardcode).
2. Login admin & petugas berfungsi; hak akses terpisah dan teruji.
3. Antrian online menerbitkan nomor unik, mencegah dobel, dan statusnya bisa diubah petugas.
4. Layar antrian memperbarui diri tanpa reload.
5. Seluruh CRUD konten berjalan dan perubahannya langsung terlihat di situs publik.
6. Tampilan rapi di lebar 375 px dan 1440 px.
