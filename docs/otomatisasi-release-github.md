# Otomatisasi GitHub Release & Tagging Versi

## 1. Konsep & Arsitektur
Sistem otomatisasi rilis dikonfigurasikan menggunakan **GitHub Actions** ([`.github/workflows/auto-release.yml`](file:///d:/WebApps/dashboard-simrs/.github/workflows/auto-release.yml)).
Setiap kali pengembang memperbarui berkas [`version.json`](file:///d:/WebApps/dashboard-simrs/version.json) (baik versi `patch`, `minor`, maupun `major`) dan melakukan push ke branch `main`:
1. GitHub Actions mendeteksi perubahan pada `version.json`.
2. Script [`scripts/generate-release-notes.php`](file:///d:/WebApps/dashboard-simrs/scripts/generate-release-notes.php) mengekstrak entri versi paling atas:
   - Nomor versi semantik (misal: `2.0.0` -> tag `v2.0.0`).
   - Judul rilis (`title`).
   - Tipe rilis (`badge`).
   - Seluruh daftar catatan perubahan (`changeLog`).
3. Workflow memverifikasi apakah Git Tag `vX.Y.Z` tersebut sudah pernah dibuat sebelumnya.
4. Jika tag belum ada, GitHub Actions secara otomatis:
   - Membuat Git Tag `vX.Y.Z` yang menunjuk tepat ke commit tersebut.
   - Menerbitkan **GitHub Release** resmi di repositori GitHub lengkap dengan release notes dan arsip source code (zip/tar.gz).

---

## 2. Cara Menggunakannya
Pengembang tidak perlu lagi membuat tag manual atau membuat rilis di GitHub web UI.
Cukup lakukan langkah standar berikut:
1. Tambahkan entri versi baru di paling atas array [`version.json`](file:///d:/WebApps/dashboard-simrs/version.json), contoh:
```json
[
    {
        "version": "2.0.1",
        "date": "2026-10-09",
        "title": "Patch Release: Perbaikan Filter dan Pemisahan TNI/POLRI",
        "badge": "Bugfix & Improvement",
        "type": "patch",
        "changeLog": [
            "Penyempurnaan pemisahan data pasien dinas...",
            "Peningkatan stabilitas..."
        ]
    },
    ...
]
```
2. Commit dan push ke branch `main`:
```bash
git add version.json
git commit -m "chore: bump version to 2.0.1"
git push origin main
```
3. GitHub Actions otomatis berjalan dan merilis `v2.0.1` di menu **Releases** GitHub repositori.
