# SILAKES — Sistem Informasi Layanan Kesehatan Puskesmas Umban Sari

Aplikasi web layanan Puskesmas Umban Sari (Pekanbaru) hasil pengembangan dari laporan
Capstone Project Kelompok 4. Berisi situs publik (profil layanan, jadwal praktik,
persyaratan, alur, pengumuman, FAQ, antrian online) dan panel pengelola untuk petugas.

Stack: **Laravel 12**, **MariaDB 11.4**, **Tailwind CSS 4** (Vite), **Alpine.js 3**.

---

## 1. Menjalankan aplikasi

Prasyarat: PHP 8.2+ dengan ekstensi `pdo_mysql`, Node.js 20+, MariaDB/MySQL.

```bash
composer install
npm install
npm run build

cp .env.example .env      # lalu sesuaikan koneksi DB
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Buka `http://127.0.0.1:8000`.

### Menjalankan di VPS ini (instalasi userland, tanpa root)

```bash
export PATH=/home/sayaahmad/.local/bin:$PATH

# 1. MariaDB (socket + port 3307)
~/.local/opt/mariadb-11.4.4-linux-systemd-x86_64/bin/mariadbd \
  --datadir=$HOME/.local/var/mariadb \
  --socket=$HOME/.local/var/run/mysqld.sock \
  --port=3307 --bind-address=127.0.0.1 \
  --log-error=$HOME/.local/var/log/mariadb.err &

# 2. Aplikasi
cd ~/PROJECTKELOMPOK4 && php artisan serve --host=0.0.0.0 --port=8000
```

---

## 2. Akun demo

| Peran   | Email                   | Password   |
|---------|-------------------------|------------|
| Admin   | `admin@silakes.test`    | `admin12345`   |
| Petugas | `petugas@silakes.test`  | `petugas12345` |

> **Ganti password ini sebelum dipakai di lingkungan nyata.**

---

## 3. Halaman publik

| URL | Isi |
|-----|-----|
| `/` | Beranda: hero, layanan unggulan, jadwal hari ini, antrian langsung |
| `/layanan` | Daftar layanan + persyaratan |
| `/layanan/{slug}` | Detail layanan (persyaratan, durasi, alur) |
| `/jadwal` | Jadwal praktik per hari, dikelompokkan per poli |
| `/alur` | Alur pelayanan dan tips |
| `/pengumuman`, `/pengumuman/{slug}` | Pengumuman puskesmas |
| `/faq` | Pertanyaan umum |
| `/kontak` | Alamat, telepon, jam layanan, peta |
| `/antrian` | Form ambil nomor antrian online |
| `/antrian/tiket/{kode}` | Tiket digital (kode, nomor, estimasi, sisa antrian) |
| `/antrian/layar` | Layar panggilan untuk ruang tunggu (auto-refresh) |
| `/antrian/status` | JSON status antrian (dipakai layar & tiket) |

## 4. Panel pengelola

`/masuk` → `/panel`

- **Dasbor** — ringkasan antrian hari ini, tren 7 hari, tombol panggil berikutnya
- **Antrian** — daftar per tanggal, aksi panggil / selesai / lewat / batal, pendaftaran loket
- **Poli, Layanan, Tenaga Kesehatan, Jadwal** — CRUD
- **Pengumuman, FAQ** — CRUD
- **Pengaturan** — identitas puskesmas, jam operasional, kontak
- **Pengguna** — CRUD akun (khusus admin)
- **Log aktivitas** — jejak aksi pengguna (khusus admin)

---

## 5. Struktur data

`users` (role `admin`/`petugas`) · `polis` · `layanans` · `dokters` · `jadwals` ·
`antrians` · `pengumumen` · `faqs` · `pengaturans` · `activity_logs`

Aturan penting:

- Kode antrian memakai huruf unik per poli: `U`mum, `G`igi, `K`IA, `A`nak, Gi`z`i, `T`B Paru.
- Nomor antrian dihitung per poli per tanggal (`max(nomor) + 1`).
- Antrian online ditolak bila poli tidak punya jadwal aktif pada hari tersebut; kartu poli
  yang tutup ditandai "Tidak praktik hari ini" dan tidak bisa dipilih.
- Estimasi jam layanan dihitung dari nomor yang sedang dipanggil × durasi layanan, dan
  tidak pernah menampilkan jam yang sudah lewat.
- Zona waktu aplikasi `Asia/Jakarta`, locale fallback `id`.

---

## 6. Batasan produk

Sesuai laporan: aplikasi **tidak** menegakkan diagnosis, **tidak** menggantikan konsultasi
tenaga kesehatan, dan **tidak** menyimpan rekam medis elektronik. Data yang disimpan
terbatas pada informasi layanan dan administrasi kunjungan (nama, NIK opsional, nomor HP
opsional, keluhan singkat, status antrian).

Data pribadi tersebut hanya disimpan selama dibutuhkan untuk pelayanan, lalu dikosongkan
otomatis. Batas simpan diatur di menu **Pengaturan → Privasi** (bawaan 90 hari) dan
disimpan sebagai `retensi_hari`. Kolom `antrians.anonim_pada` mencatat kapan sebuah baris
dianonimkan. Menjalankan manual:

```bash
php artisan antrian:anonimkan              # pakai batas dari pengaturan
php artisan antrian:anonimkan --hari=30    # batas khusus
php artisan antrian:anonimkan --dry-run    # hitung saja
```

Penjadwalan harian ada di `routes/console.php` (`Schedule::command('antrian:anonimkan')->dailyAt('01:00')`),
jadi pada instalasi nyata scheduler Laravel harus aktif:

```
* * * * * cd /path-ke-aplikasi && php artisan schedule:run >> /dev/null 2>&1
```

Yang dikosongkan: `nama_pasien` (diganti teks `Pasien`), `nik`, `no_hp`, dan `keluhan`.
Nomor antrian, kode, status, dan tanggal **tetap** supaya statistik dan rata-rata waktu
tunggu di dasbor tidak rusak.

---

## 7. Dokumentasi terkait

- `docs/PRD.md` — kebutuhan produk, persona, alur, kriteria terima
- `docs/PANDUAN.md` — dokumen ini
