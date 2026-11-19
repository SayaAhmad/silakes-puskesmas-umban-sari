# Deploy SILAKES ke server Ubuntu dengan Docker

Panduan ini untuk server Ubuntu yang diakses langsung lewat `http://IP_SERVER:8080`,
tanpa domain dan tanpa HTTPS. Cocok untuk demo, tugas, atau jaringan internal.

> Data pasien (nama, NIK, nomor HP, keluhan) pada mode ini terkirim tanpa enkripsi.
> Untuk penggunaan nyata, pasang domain dan HTTPS lebih dulu.

## 1. Siapkan server

```bash
sudo apt install -y ca-certificates curl git ufw

# Docker Engine + plugin compose
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker "$USER"
```

Logout lalu login kembali agar grup `docker` aktif, kemudian cek:

```bash
docker --version
docker compose version
```

## 2. Firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow 8080/tcp
sudo ufw enable
sudo ufw status
```

Port 3306 (MySQL) tidak perlu dibuka; container database tidak meneruskan port ke host.

## 3. Ambil kode

```bash
git clone https://github.com/SayaAhmad/silakes-puskesmas-umban-sari.git silakes
cd silakes
git log --oneline -1
```

## 4. Siapkan konfigurasi

```bash
cp .env.server.example .env
```

Isi `.env`:

```bash
# APP_URL memakai IP publik server
APP_URL=http://203.0.113.10:8080

# Kata sandi acak
openssl rand -hex 24   # tempel ke DB_PASSWORD
openssl rand -hex 24   # tempel ke DB_ROOT_PASSWORD
```

Buat `APP_KEY`:

```bash
docker compose -f docker-compose.server.yml run --rm app php artisan key:generate --show
```

Tempel hasilnya ke `.env` pada baris `APP_KEY=`. Larangan: jangan gunakan `APP_KEY` dari
mesin developer.

Pastikan `.env` tidak ikut ter-commit:

```bash
git status --short   # .env tidak boleh muncul
```

## 5. Build dan jalankan

```bash
docker compose -f docker-compose.server.yml up -d --build
docker compose -f docker-compose.server.yml ps
```

Tunggu sampai `app` berstatus `healthy` (sekitar 40 detik pada start pertama).
Migrasi dijalankan otomatis oleh entrypoint.

## 6. Isi data awal

Hanya sekali, pada database yang masih kosong:

```bash
docker compose -f docker-compose.server.yml exec app php artisan db:seed --force
```

Akun bawaan seeder:

| Email | Kata sandi | Peran |
|---|---|---|
| `admin@silakes.test` | `admin12345` | admin |
| `petugas@silakes.test` | `petugas12345` | petugas |

**Segera ganti kata sandi kedua akun ini** lewat menu Panel > Pengguna.

## 7. Verifikasi

```bash
curl -i http://localhost:8080/up        # harus 200
curl -s -o /dev/null -w '%{http_code}\n' http://localhost:8080/
```

Dari komputer lain, buka `http://IP_SERVER:8080`.

## 8. Perintah harian

Semua perintah memakai flag `-f docker-compose.server.yml`. Agar ringkas, buat alias:

```bash
echo "alias sc='docker compose -f docker-compose.server.yml'" >> ~/.bashrc
source ~/.bashrc
```

| Kebutuhan | Perintah |
|---|---|
| Status | `sc ps` |
| Log aplikasi | `sc logs -f app` |
| Log 100 baris terakhir | `sc logs --tail 100 app` |
| Start | `sc up -d` |
| Stop (data tetap) | `sc down` |
| Restart aplikasi | `sc restart app` |
| Artisan | `sc exec app php artisan migrate:status` |
| Status scheduler | `sc exec app supervisorctl status` |

## 9. Update aplikasi

```bash
cd ~/silakes
git pull
docker compose -f docker-compose.server.yml up -d --build
sc logs -f app
```

Migrasi berjalan otomatis. Kode baru tidak dipakai sebelum image di-build ulang karena
`opcache.validate_timestamps=0` dan kode berada di dalam image.

## 10. Backup

Simpan keluar dari direktori proyek agar tidak ikut terhapus.

```bash
mkdir -p ~/backup/silakes

# Database
docker compose -f docker-compose.server.yml exec -T db \
  sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" --single-transaction --quick "$MYSQL_DATABASE"' \
  | gzip > ~/backup/silakes/db-$(date +%F).sql.gz

# Storage aplikasi (log, file privat)
docker run --rm \
  -v silakes-server_silakes-server-storage:/data \
  -v ~/backup/silakes:/backup \
  alpine tar czf /backup/storage-$(date +%F).tar.gz -C /data .
```

Uji restore di lingkungan terpisah sebelum mengandalkannya:

```bash
gunzip -c ~/backup/silakes/db-2026-11-20.sql.gz | \
  docker compose -f docker-compose.server.yml exec -T db \
  sh -c 'mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"'
```

Jadwalkan lewat cron:

```bash
crontab -e
# 0 2 * * * cd /home/USER/silakes && docker compose -f docker-compose.server.yml exec -T db sh -c 'mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" --single-transaction --quick "$MYSQL_DATABASE"' | gzip > /home/USER/backup/silakes/db-$(date +\%F).sql.gz
```

## Pemecahan masalah

**Halaman 500 atau "No application encryption key"**
`APP_KEY` kosong. Ulangi langkah 4 lalu `sc up -d --force-recreate app`.

**`/up` tidak merespons, `sc ps` menampilkan `unhealthy`**
```bash
sc logs --tail 100 app
sc exec app supervisorctl status    # nginx, php-fpm, scheduler harus RUNNING
```

**Port 8080 sudah dipakai**
```bash
sudo ss -lptn 'sport = :8080'
```
Ganti `APP_PORT` di `.env`, lalu `sc up -d`.

**Tidak bisa diakses dari komputer lain**
Periksa `sudo ufw status` dan pastikan port dibuka di security group penyedia VPS.

**Error koneksi database saat start**
Entrypoint mencoba 10 kali. Bila tetap gagal, cek `sc logs db` dan pastikan
`DB_PASSWORD` sama di `app` dan `db`.

**Login berhasil tapi langsung terlempar ke halaman masuk**
Cookie tidak tersimpan. Pastikan `APP_URL` memakai host yang sama dengan yang dibuka di
browser (IP, bukan `localhost`).

## Menghapus semua data

```bash
docker compose -f docker-compose.server.yml down -v
```

Perintah ini menghapus container **dan** volume database serta storage secara permanen.

## Langkah lanjutan: HTTPS

Bila nanti tersedia domain, pola yang direkomendasikan:

1. Pointing DNS ke IP server.
2. Ganti `ports` menjadi `127.0.0.1:8080:80` agar hanya reverse proxy yang bisa menjangkau app.
3. Pasang Caddy atau Nginx + certbot di host untuk terminasi TLS.
4. Set `APP_URL=https://domain-anda.com` dan `SESSION_SECURE_COOKIE=true`.
5. Buka port 80 dan 443, tutup 8080 dari publik.
