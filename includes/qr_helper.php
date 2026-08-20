<?php
// ============================================
// GoalTicket - QR Code helper (Endroid 5.x)
// ============================================

require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

function generateQrDataUri(string $data): string {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($data)
        ->size(300)
        ->margin(10)
        ->build();

    return $result->getDataUri();
}

function generateQrPng(string $data): string {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($data)
        ->size(300)
        ->margin(10)
        ->build();

    return $result->getString();
}
?>
