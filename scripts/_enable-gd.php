<?php

$file = 'C:/xampp/php/php.ini';

$content = file_get_contents($file);

if ($content === false) {
    fwrite(STDERR, "Gagal membaca php.ini\n");
    exit(1);
}

if (str_contains($content, "\nextension=gd\n") || str_starts_with($content, "extension=gd\n")) {
    echo "extension=gd sudah aktif\n";
    exit(0);
}

$replaced = str_replace("\n;extension=gd", "\nextension=gd", $content);

if ($replaced === $content) {
    fwrite(STDERR, "Baris ;extension=gd tidak ditemukan\n");
    exit(1);
}

if (file_put_contents($file, $replaced) === false) {
    fwrite(STDERR, "Gagal menulis php.ini\n");
    exit(1);
}

echo "extension=gd diaktifkan\n";