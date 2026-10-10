---
name: version-release
description: >-
  Wajib dipakai setiap kali merilis versi baru (patch, minor, major) pada Dashboard SIMRS. Memastikan entri version.json memiliki label versi (badge/type) dan deskripsi (title + changeLog), lalu commit, tag, dan push agar GitHub Release otomatis terbuat.
---

# Version Release Guide

## Aturan
- Entri baru ditaruh **paling atas** `version.json` (versi aktif = indeks 0).
- Setiap entri **wajib** memiliki: `version`, `date`, `title` (deskripsi singkat rilis), `badge` (label), `type`, `changeLog` (daftar deskripsi perubahan).
- Label & tipe:
  | type | badge | Kapan |
  |---|---|---|
  | `patch` | `Patch Release` | Perbaikan bug |
  | `minor` | `Minor Release` | Fitur baru kompatibel |
  | `major` | `Major Release` | Perubahan besar/breaking |
- `title` diawali label, mis. `Minor Release: Ringkasan fitur`.
- `changeLog` berbahasa Indonesia, satu kalimat per perubahan.

## Langkah
1. Naikkan versi semantik & tambah entri di `version.json`.
2. Sesuaikan: badge + tabel riwayat `README.md`, serta tes yang memuat nomor versi (`tests/Feature/ChangeLogTest.php`, `LoginTest.php`).
3. Jalankan `php artisan test tests/Feature/ChangeLogTest.php tests/Feature/LoginTest.php`.
4. `git add -A; git commit -m "feat: release vX.Y.Z - ringkasan"; git tag vX.Y.Z; git push origin main --tags`.
5. GitHub Actions `auto-release.yml` membuat Release bernama `vX.Y.Z - <title>` dengan isi dari `scripts/generate-release-notes.php` (label + deskripsi + changelog).
