<?php

$versionFile = __DIR__ . '/../version.json';

if (!file_exists($versionFile)) {
    fwrite(STDERR, "Error: File version.json tidak ditemukan.\n");
    exit(1);
}

$content = file_get_contents($versionFile);
$json = json_decode($content, true);

if (empty($json) || !is_array($json)) {
    fwrite(STDERR, "Error: Isi version.json tidak valid atau kosong.\n");
    exit(1);
}

// Pilih entri berdasarkan argumen versi (opsional), default entri teratas
$wanted = ltrim($argv[1] ?? '', 'v');
$latest = $json[0];
if ($wanted !== '') {
    foreach ($json as $entry) {
        if (ltrim($entry['version'] ?? '', 'v') === $wanted) {
            $latest = $entry;
            break;
        }
    }
}
$version = trim($latest['version'] ?? '');
$title = trim($latest['title'] ?? "Release v{$version}");
$badge = trim($latest['badge'] ?? '');
$type = trim($latest['type'] ?? 'minor');
$date = trim($latest['date'] ?? date('Y-m-d'));
$changelogs = $latest['changeLog'] ?? [];

if ($version === '') {
    fwrite(STDERR, "Error: Nomor version tidak ditemukan pada entri teratas version.json.\n");
    exit(1);
}

$body = "### {$title}\n\n";
$body .= "- **Versi:** `v{$version}`\n";
$body .= "- **Tanggal Rilis:** `{$date}`\n";
if ($badge !== '') {
    $body .= "- **Tipe Rilis:** `{$badge}` (`{$type}`)\n";
}
$body .= "\n#### Catatan Pembaruan (Changelog):\n";
foreach ($changelogs as $item) {
    $body .= "- " . trim($item) . "\n";
}

$body .= "\n---\n*Dibuat secara otomatis oleh GitHub Actions dari berkas `version.json`.* \n";

// Simpan catatan rilis ke RELEASE_NOTES.md
file_put_contents(__DIR__ . '/../RELEASE_NOTES.md', $body);

// Jika dijalankan di lingkungan GitHub Actions, set output environment
$githubOutput = getenv('GITHUB_OUTPUT');
if ($githubOutput && file_exists($githubOutput)) {
    file_put_contents($githubOutput, "version={$version}\n", FILE_APPEND);
    file_put_contents($githubOutput, "tag_name=v{$version}\n", FILE_APPEND);
    file_put_contents($githubOutput, "release_title={$title}\n", FILE_APPEND);
}

echo "Berhasil mengekstrak rilis v{$version} - {$title}\n";
