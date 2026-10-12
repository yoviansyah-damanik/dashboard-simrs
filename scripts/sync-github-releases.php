<?php

/**
 * Script untuk sinkronisasi / pembuatan GitHub Release otomatis dari version.json.
 * Dijalankan di GitHub Actions runner atau lokal dengan gh CLI terpasang.
 */

$versionFile = __DIR__ . '/../version.json';
if (!file_exists($versionFile)) {
    fwrite(STDERR, "Error: version.json tidak ditemukan.\n");
    exit(1);
}

$entries = json_decode(file_get_contents($versionFile), true);
if (empty($entries) || !is_array($entries)) {
    fwrite(STDERR, "Error: version.json kosong atau tidak valid.\n");
    exit(1);
}

$latestVersion = trim($entries[0]['version'] ?? '');

// Balik urutan agar rilis dibuat berurutan secara kronologis (dari tertua ke terbaru)
$chronologicalEntries = array_reverse($entries);

echo "Memeriksa status GitHub Release untuk " . count($chronologicalEntries) . " versi...\n";

foreach ($chronologicalEntries as $entry) {
    $version = trim($entry['version'] ?? '');
    if ($version === '') {
        continue;
    }

    $tag = "v{$version}";
    $title = trim($entry['title'] ?? "Release {$tag}");
    $badge = trim($entry['badge'] ?? '');
    $type = trim($entry['type'] ?? 'minor');
    $date = trim($entry['date'] ?? date('Y-m-d'));
    $changelogs = $entry['changeLog'] ?? [];

    echo "\n----------------------------------------\n";
    echo "Memeriksa {$tag}: {$title}\n";

    // 1. Cek apakah GitHub Release sudah ada
    $checkCmd = sprintf('gh release view %s >/dev/null 2>&1', escapeshellarg($tag));
    passthru($checkCmd, $checkCode);

    if ($checkCode === 0) {
        echo "Release {$tag} sudah ada di GitHub. Dilewati.\n";
        continue;
    }

    echo "Release {$tag} belum ada. Membuat release notes...\n";

    // 2. Buat isi release notes
    $body = "### {$title}\n\n";
    $body .= "- **Versi:** `{$tag}`\n";
    $body .= "- **Tanggal Rilis:** `{$date}`\n";
    if ($badge !== '') {
        $body .= "- **Tipe Rilis:** `{$badge}` (`{$type}`)\n";
    }
    $body .= "\n#### Catatan Pembaruan (Changelog):\n";
    if (!empty($changelogs)) {
        foreach ($changelogs as $item) {
            $body .= "- " . trim($item) . "\n";
        }
    } else {
        $body .= "- Pembaruan sistem SIMRS.\n";
    }
    $body .= "\n---\n*Dibuat secara otomatis oleh GitHub Actions dari berkas `version.json`.* \n";

    $notesFile = __DIR__ . "/../RELEASE_NOTES_{$version}.md";
    file_put_contents($notesFile, $body);

    // 3. Tentukan opsi perintah gh release create
    $isLatest = ($version === $latestVersion);
    $releaseTitle = "{$tag} - {$title}";

    $cmdArgs = [
        'gh',
        'release',
        'create',
        escapeshellarg($tag),
        '--title', escapeshellarg($releaseTitle),
        '--notes-file', escapeshellarg($notesFile),
        $isLatest ? '--latest' : '--latest=false'
    ];

    // Cek apakah tag sudah ada di remote atau local git
    $tagExists = false;
    exec(sprintf('git rev-parse --verify %s 2>/dev/null', escapeshellarg("refs/tags/{$tag}")), $outLocal, $codeLocal);
    if ($codeLocal === 0) {
        $tagExists = true;
    } else {
        exec(sprintf('git ls-remote --tags origin %s 2>/dev/null', escapeshellarg("refs/tags/{$tag}")), $outRemote, $codeRemote);
        if (!empty($outRemote)) {
            $tagExists = true;
        }
    }

    // Jika tag belum ada, cari commit pertama saat versi ini muncul di version.json
    if (!$tagExists) {
        $grepPattern = sprintf('"version": "%s"', $version);
        $findCommitCmd = sprintf('git log -S%s --format=%%H --reverse -- version.json 2>/dev/null', escapeshellarg($grepPattern));
        exec($findCommitCmd, $commits);
        $targetCommit = !empty($commits) ? trim($commits[0]) : '';
        if ($targetCommit !== '') {
            $cmdArgs[] = '--target';
            $cmdArgs[] = escapeshellarg($targetCommit);
            echo "Menetapkan commit target: {$targetCommit}\n";
        }
    }

    $cmd = implode(' ', $cmdArgs);
    echo "Menjalankan: {$cmd}\n";

    passthru($cmd, $exitCode);

    if (file_exists($notesFile)) {
        unlink($notesFile);
    }

    if ($exitCode === 0) {
        echo "Berhasil membuat GitHub Release {$tag}!\n";
    } else {
        echo "PERINGATAN: Gagal membuat GitHub Release {$tag} (exit code: {$exitCode}).\n";
    }
}

echo "\nProses sinkronisasi GitHub Releases selesai.\n";
