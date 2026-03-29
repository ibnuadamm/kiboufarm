<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use App\Models\Pembelian;
use App\Models\KematianIkan;
use App\Models\Jurnal;
use App\Models\Vendor;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup Unique Data to avoid Global Average collision
$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Link Test']);
$bibit = Persediaan::create(['nama_barang' => 'Bibit Specific Link ' . rand(100,999), 'kategori' => 'Bibit', 'satuan' => 'ekor']);

// Scenario: 
// Purchase 1000 @ 2500 IDR (Total 2.500.000)
// Create Kolam #881
// Link them (Controller Logic)

$price = 2500;
$qty = 1000;
$total = $price * $qty;

$pembelian = Pembelian::create([
    'persediaan_id' => $bibit->id,
    'vendor_id' => $vendor->id,
    'jumlah' => $qty,
    'harga_satuan' => $price,
    'total_harga' => $total,
    'tanggal_pembelian' => now()
]);

$kolam = Kolam::create([
    'nomor_kolam' => 881,
    'persediaan_id' => $bibit->id,
    'tanggal_tebar' => now(),
    'jumlah' => $qty,
    'status' => 'masuk_bibit'
]);

// SIMULATE THE FIX: Linkage
$pembelian->kolam_id = $kolam->id;
$pembelian->save();

echo "Setup: Pembelian linked to Kolam " . $kolam->id . " (Price: $price)\n";

// Trigger Kematian
$mati = 10;
echo "Kematian: $mati ekor\n";
$kematian = KematianIkan::create([
    'kolam_id' => $kolam->id,
    'jumlah_ekor' => $mati,
    'tanggal' => now(),
    'catatan' => 'Test Linkage'
]);

// Verify Journal
$expected = $mati * $price; // 10 * 2500 = 25.000
$jurnal = Jurnal::where('ref_type', KematianIkan::class)->where('ref_id', $kematian->id)->with('details')->first();

if ($jurnal) {
    $debit = $jurnal->details->where('debit', '>', 0)->first()->debit;
    echo "Journal Debit: " . $debit . " (Expected: $expected) ... " . ($debit == $expected ? "PASS" : "FAIL") . "\n";
} else {
    echo "[FAIL] No Journal Created\n";
}

// Cleanup
if ($jurnal) { $jurnal->details()->delete(); $jurnal->delete(); }
$kematian->delete();
$pembelian->delete();
$kolam->delete();
$bibit->delete();

