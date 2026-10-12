# Otomatisasi GitHub Release & Tagging Versi

## 1. Konsep & Arsitektur
Sistem otomatisasi rilis dikonfigurasikan menggunakan **GitHub Actions** ([`.github/workflows/auto-release.yml`](file:///d:/WebApps/dashboard-simrs/.github/workflows/auto-release.yml)) dan script sinkronisasi [php `scripts/sync-github-releases.php`](file:///d:/WebApps/dashboard-simrs/scripts/sync-github-releases.php).
Setiap kali pengembang memperbarui berkas [`version.json`](file:///d:/WebApps/dashboard-simrs/version.json) (baik versi `patch`, `minor`, maupun `major`) dan melakukan push ke branch `main`:
1. GitHub Actions mendeteksi push pada branch `main` atau trigger `workflow_dispatch`.
2. Script [`scripts/sync-github-releases.php`](file:///d:/WebApps/dashboard-simrs/scripts/sync-github-releases.php) memindai semua entri di `version.json` secara kronologis.
3. Untuk setiap versi yang belum memiliki GitHub Release resmi:
   - Membuat catatan rilis terstruktur menggunakan [`scripts/generate-release-notes.php`](file:///d:/WebApps/dashboard-simrs/scripts/generate-release-notes.php).
   - Memeriksa Git Tag `vX.Y.Z`; jika tag belum ada, membuat tag ke commit spesifik versi tersebut.
   - Menerbitkan **GitHub Release** resmi via `gh release create` dengan judul, label tipe rilis, tanggal rilis, dan daftar perubahan (`changeLog`).
   - Versi teratas di `version.json` otomatis ditandai sebagai `--latest`.

---

## 2. Cara Menggunakannya
Pengembang tidak perlu lagi membuat rilis manual di GitHub web UI.
Cukup lakukan langkah rilis sesuai panduan skill `.agents/skills/version-release/`:
1. Tambahkan entri versi baru di paling atas array [`version.json`](file:///d:/WebApps/dashboard-simrs/version.json):
```json
[
    {
        "version": "2.1.3",
        "date": "2026-10-12",
        "title": "Patch Release: Deskripsi singkat perbaikan",
        "badge": "Patch Release",
        "type": "patch",
        "changeLog": [
            "Ringkasan perbaikan atau fitur baru...",
            "Perbaikan lainnya..."
        ]
    },
    ...
]
```
2. Sesuaikan badge & tabel riwayat di [`README.md`](file:///d:/WebApps/dashboard-simrs/README.md).
3. Jalankan pengujian `php artisan test`.
4. Commit dan push ke branch `main`:
```bash
git add version.json README.md
git commit -m "feat: release v2.1.3 - Ringkasan rilis"
git tag v2.1.3
git push origin main --tags
```
5. GitHub Actions otomatis membuat **GitHub Release** resmi di menu **Releases** GitHub repositori.
