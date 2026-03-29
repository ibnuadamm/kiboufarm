<?php

use App\Models\Kolam;
use App\Models\KematianIkan;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Assuming Kolam 999 exists from previous test
$kolam = Kolam::where('nomor_kolam', 999)->first();
if (!$kolam) {
    echo "Kolam 999 not found, skipping.\n";
    exit;
}

echo "Kolam 999 Data:\n";
echo "- Jumlah (Sisa, stored): " . $kolam->jumlah . "\n";
echo "- Jumlah Mati (Stored): " . $kolam->jumlah_mati . "\n";
echo "- Jumlah Awal (Calculated): " . ($kolam->jumlah + $kolam->jumlah_mati) . "\n";

// Manual check logic
// Initial was 1000. Mati 50.
// Expected: Sisa 950, Mati 50, Awal 1000.
$expectedAwal = 1000;
$calculatedAwal = $kolam->jumlah + $kolam->jumlah_mati;

if ($calculatedAwal == $expectedAwal) {
    echo "[PASS] Formula (Sisa + Mati = Awal) holds true.\n";
} else {
    echo "[FAIL] Formula mismatch. Calculated: $calculatedAwal, Expected: $expectedAwal\n";
}
