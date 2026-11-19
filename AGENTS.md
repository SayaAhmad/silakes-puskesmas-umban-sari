# AGENTS.md

## Git

- Setiap perubahan file yang dibuat agent harus di-commit. Jangan tinggalkan perubahan menggantung di working tree.
- Sebelum push, selalu minta konfirmasi eksplisit ke pengguna: tampilkan commit yang akan didorong (hash + judul), branch tujuan, dan jumlah commit tertunda.
- Jangan push tanpa persetujuan, termasuk `git push --force`, `git push --tags`, atau push ke branch lain.
- Sebelum push, jalankan `git fetch` dan laporkan bila branch lokal tertinggal atau remote punya commit baru.
- Pesan commit memakai Conventional Commits dalam bahasa Indonesia, contoh: `feat(antrian): tolak NIK dobel`, `fix(dasbor): hitung rata-rata waktu tunggu`, `chore(docker): tambah Dockerfile`.
- Jangan commit file rahasia: `.env`, `storage/*.key`, kredensial database.
