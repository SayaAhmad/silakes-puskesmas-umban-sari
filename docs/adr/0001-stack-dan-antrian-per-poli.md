# ADR 0001 — SILAKES: Laravel 12 + MariaDB userland, antrian per poli

- Status: diterima
- Tanggal: 2026-11-17
- Konteks: tugas capstone Kelompok 4; harus Laravel + MySQL, ada login admin, tampilan
  tidak boleh template biasa; dikerjakan di VPS tanpa akses root.

## Keputusan

1. **Stack**: Laravel 12 (PHP 8.4 statis) + MariaDB 11.4 + Blade/Tailwind 4 + Alpine 3.
2. **Instalasi tanpa root**: PHP statis di `~/.local/bin`, MariaDB tarball di `~/.local/opt`,
   data di `~/.local/var`. Alasan: `sudo` meminta password, tidak ada paket sistem.
3. **DB di port 3307**, bind `127.0.0.1`, socket `~/.local/var/run/mysqld.sock`.
   Alasan: hindari konflik dengan MySQL sistem bila kelak ada.
4. **Antrian per poli**: nomor dihitung `max(nomor)+1` per poli per tanggal; kode memakai
   huruf unik per poli (`U/G/K/A/Z/T`). Alasan: satu nomor global membuat estimasi dan
   panggilan antar-poli kacau; huruf unik mencegah Gigi dan Gizi bertabrakan.
5. **Validasi jadwal**: antrian online ditolak bila poli tidak punya jadwal aktif hari itu;
   kartu poli yang tutup dinonaktifkan di form. Alasan: mencegah warga datang sia-sia.
6. **Estimasi** dihitung dari nomor yang sedang dipanggil × durasi layanan, tidak pernah
   menampilkan jam yang sudah lewat. Alasan: estimasi berbasis jam buka poli selalu basi.
7. **Zona waktu `Asia/Jakarta`**, locale fallback `id`. Alasan: default Laravel UTC membuat
   jam layar panggilan meleset 7 jam.
8. **Panel terpisah dari situs publik** (`/panel` vs `/`), middleware `aktif` + `admin`.
   Alasan: petugas tidak perlu menyentuh halaman publik, dan akun nonaktif langsung diputus.

## Konsekuensi

- Aplikasi hanya bisa dijalankan oleh user `sayaahmad` (tidak ada service sistem).
- `php artisan serve` tidak untuk produksi; bila demo publik diperlukan, perlu reverse
  proxy/tunnel (belum dipasang).
- Password akun seeder (`password`) wajib diganti sebelum dipakai nyata.
