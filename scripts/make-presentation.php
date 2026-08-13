<?php

/**
 * Generate the presentation PPTX for the E-Commerce project.
 *
 * Usage: php scripts/make-presentation.php
 */

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\AutoShape;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

const INK = 'FF0A0A0A';
const PAPER = 'FFFFFFFF';
const GRAY = 'FF737373';
const LIGHT = 'FFF5F5F5';

$ppt = new PhpPresentation();
$ppt->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9);

/** Add a full-slide background rectangle. */
function addBackground(PhpPresentation $ppt, string $color): void
{
    $slide = $ppt->getActiveSlide();
    $shape = $slide->createAutoShape(AutoShape::TYPE_RECTANGLE);
    $shape->setWidth($ppt->getLayout()->getCX());
    $shape->setHeight($ppt->getLayout()->getCY());
    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($color));
    $shape->getBorder()->setLineStyle(Border::LINE_NONE);
}

/** Add a full-width bar (title strip or accent). */
function addBar(PhpPresentation $ppt, int $height, string $color, int $y = 0): void
{
    $slide = $ppt->getActiveSlide();
    $shape = $slide->createAutoShape(AutoShape::TYPE_RECTANGLE);
    $shape->setWidth($ppt->getLayout()->getCX());
    $shape->setHeight($height);
    $shape->setOffsetX(0);
    $shape->setOffsetY($y);
    $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($color));
    $shape->getBorder()->setLineStyle(Border::LINE_NONE);
}

/** Add a text shape with a list of styled lines. */
function addText(PhpPresentation $ppt, int $x, int $y, int $w, int $h, array $lines, string $align = Alignment::HORIZONTAL_LEFT): void
{
    $slide = $ppt->getActiveSlide();
    $shape = $slide->createRichTextShape();
    $shape->setWidth($w);
    $shape->setHeight($h);
    $shape->setOffsetX($x);
    $shape->setOffsetY($y);

    if ($align === Alignment::HORIZONTAL_CENTER) {
        $shape->setVerticalAlignCenter(true);
    }

    foreach ($lines as $line) {
        $textRun = $shape->createTextRun($line['text']);
        $textRun->getFont()
            ->setSize($line['size'] ?? 18)
            ->setBold($line['bold'] ?? false)
            ->setColor(new Color($line['color'] ?? INK));

        $paragraph = $shape->getActiveParagraph();
        $paragraph->getAlignment()->setHorizontal($align);
        $paragraph->setLineSpacing(1.15);
    }
}

function addSlide(PhpPresentation $ppt): void
{
    $ppt->createSlide();
}

/* ------------------------------------------------------------------ */
/* Slide 1 - Cover                                                      */
/* ------------------------------------------------------------------ */
addBackground($ppt, INK);
addText($ppt, 600000, 2800000, 11000000, 2400000, [
    ['text' => 'ZAKY STORE', 'size' => 60, 'bold' => true, 'color' => PAPER],
], Alignment::HORIZONTAL_CENTER);
addText($ppt, 600000, 4400000, 11000000, 1200000, [
    ['text' => 'Aplikasi E-Commerce dengan Laravel 12', 'size' => 26, 'color' => 'FFD4D4D4'],
], Alignment::HORIZONTAL_CENTER);
addText($ppt, 600000, 6200000, 11000000, 900000, [
    ['text' => 'Manajemen Produk • Kategori • Keranjang • Checkout • Laporan Penjualan', 'size' => 14, 'color' => 'FF9C9CA1'],
], Alignment::HORIZONTAL_CENTER);

/* ------------------------------------------------------------------ */
/* Slide 2 - Latar & Tujuan                                             */
/* ------------------------------------------------------------------ */
addSlide($ppt);
addBackground($ppt, PAPER);
addBar($ppt, 220000, INK);
addText($ppt, 500000, 520000, 11000000, 900000, [
    ['text' => 'Latar Belakang & Tujuan', 'size' => 30, 'bold' => true, 'color' => PAPER],
]);
addText($ppt, 500000, 2000000, 11000000, 4200000, [
    ['text' => '• Membangun toko online lengkap: katalog s/d laporan penjualan', 'size' => 19],
    ['text' => '• Memenuhi fitur spesifik e-commerce (5 modul utama)', 'size' => 19],
    ['text' => '• Memenuhi standar kelayakan sistem (RBAC, validasi, media, navigasi, responsif)', 'size' => 19],
    ['text' => '• Kode rapi, aman, dan teruji otomatis', 'size' => 19],
]);

/* ------------------------------------------------------------------ */
/* Slide 3 - Fitur E-Commerce                                           */
/* ------------------------------------------------------------------ */
addSlide($ppt);
addBackground($ppt, PAPER);
addBar($ppt, 220000, INK);
addText($ppt, 500000, 520000, 11000000, 900000, [
    ['text' => 'Fitur Spesifik E-Commerce', 'size' => 30, 'bold' => true, 'color' => PAPER],
]);
addText($ppt, 500000, 2000000, 11000000, 4400000, [
    ['text' => '1. Manajemen Produk — CRUD, harga, stok, gambar, aktif/nonaktif', 'size' => 19],
    ['text' => '2. Pengelompokan Kategori — filter katalog per kategori', 'size' => 19],
    ['text' => '3. Shopping Cart — qty inline, batas stok, tersimpan per user', 'size' => 19],
    ['text' => '4. Checkout — Transfer / COD, snapshot harga, riwayat pesanan', 'size' => 19],
    ['text' => '5. Laporan Penjualan — filter tanggal & status, cetak', 'size' => 19],
]);

/* ------------------------------------------------------------------ */
/* Slide 4 - Standar Kelayakan                                          */
/* ------------------------------------------------------------------ */
addSlide($ppt);
addBackground($ppt, PAPER);
addBar($ppt, 220000, INK);
addText($ppt, 500000, 520000, 11000000, 900000, [
    ['text' => 'Standar Kelayakan Sistem', 'size' => 30, 'bold' => true, 'color' => PAPER],
]);
addText($ppt, 500000, 2000000, 11000000, 4400000, [
    ['text' => '• Autentikasi & RBAC — role admin/customer, middleware role:admin', 'size' => 18],
    ['text' => '• Validasi dua sisi — FormRequest di server + JS UX di client', 'size' => 18],
    ['text' => '• Manajemen media — upload jpg/png/webp maks 2MB via Storage', 'size' => 18],
    ['text' => '• Navigasi data — pagination + pencarian/filter di semua tabel', 'size' => 18],
    ['text' => '• Admin responsif — sidebar hitam, drawer mobile, rounded & shadow', 'size' => 18],
]);

/* ------------------------------------------------------------------ */
/* Slide 5 - Arsitektur & Database                                      */
/* ------------------------------------------------------------------ */
addSlide($ppt);
addBackground($ppt, PAPER);
addBar($ppt, 220000, INK);
addText($ppt, 500000, 520000, 11000000, 900000, [
    ['text' => 'Arsitektur & Basis Data', 'size' => 30, 'bold' => true, 'color' => PAPER],
]);
addText($ppt, 500000, 2000000, 11000000, 4400000, [
    ['text' => '• Laravel 12 MVC • PHP 8.2 • MySQL • Blade + Tailwind + Alpine', 'size' => 19],
    ['text' => '• 7 tabel: users, categories, products, cart_items, orders, order_items', 'size' => 19],
    ['text' => '• Foreign key + unique constraint + index kolom filter', 'size' => 19],
    ['text' => '• Checkout: DB::transaction + lockForUpdate (anti oversell)', 'size' => 19],
    ['text' => '• Desain monokrom: ink #0A0A0A, paper #FAFAFA, rounded 2xl/3xl', 'size' => 19],
]);

/* ------------------------------------------------------------------ */
/* Slide 6 - Keamanan                                                   */
/* ------------------------------------------------------------------ */
addSlide($ppt);
addBackground($ppt, PAPER);
addBar($ppt, 220000, INK);
addText($ppt, 500000, 520000, 11000000, 900000, [
    ['text' => 'Keamanan & Kualitas Kode', 'size' => 30, 'bold' => true, 'color' => PAPER],
]);
addText($ppt, 500000, 2000000, 11000000, 4400000, [
    ['text' => '• CSRF token seluruh form • escape Blade anti-XSS', 'size' => 18],
    ['text' => '• Parameter binding anti SQL injection • password bcrypt', 'size' => 18],
    ['text' => '• Anti-IDOR: kepemilikan cart/pesanan diperiksa di server (404)', 'size' => 18],
    ['text' => '• Error handling aman tanpa kebocoran internal', 'size' => 18],
    ['text' => '• Clean code: Pint, type declaration, Laravel conventions', 'size' => 18],
]);

/* ------------------------------------------------------------------ */
/* Slide 7 - Pengujian                                                  */
/* ------------------------------------------------------------------ */
addSlide($ppt);
addBackground($ppt, PAPER);
addBar($ppt, 220000, INK);
addText($ppt, 500000, 520000, 11000000, 900000, [
    ['text' => 'Pengujian Otomatis', 'size' => 30, 'bold' => true, 'color' => PAPER],
]);
addText($ppt, 500000, 2100000, 11000000, 4400000, [
    ['text' => '59 test • 158 assertion — SEMUA LULUS', 'size' => 26, 'bold' => true, 'color' => INK],
    ['text' => '', 'size' => 8],
    ['text' => '• RBAC: customer 403 di /admin, guest redirect login', 'size' => 18],
    ['text' => '• Cart: batas stok, akumulasi qty, anti-IDOR', 'size' => 18],
    ['text' => '• Checkout: sukses, rollback stok kurang, total multi-item, validation', 'size' => 18],
    ['text' => '• Admin: CRUD produk/kategori, upload invalid ditolak, status, laporan', 'size' => 18],
]);

/* ------------------------------------------------------------------ */
/* Slide 8 - Penutup                                                    */
/* ------------------------------------------------------------------ */
addSlide($ppt);
addBackground($ppt, INK);
addText($ppt, 600000, 3200000, 11000000, 1800000, [
    ['text' => 'Terima Kasih', 'size' => 52, 'bold' => true, 'color' => PAPER],
], Alignment::HORIZONTAL_CENTER);
addText($ppt, 600000, 5200000, 11000000, 900000, [
    ['text' => 'Zaky Store — Dibangun dengan Laravel 12', 'size' => 18, 'color' => 'FFD4D4D4'],
], Alignment::HORIZONTAL_CENTER);

/* ------------------------------------------------------------------ */
$outputDir = __DIR__ . '/../docs';
if (! is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$file = $outputDir . '/Presentasi-E-Commerce-ZakyStore.pptx';
IOFactory::createWriter($ppt, 'PowerPoint2007')->save($file);

echo "PPTX berhasil dibuat: {$file}" . PHP_EOL;
echo 'Ukuran: ' . number_format(filesize($file) / 1024, 1) . ' KB' . PHP_EOL;
