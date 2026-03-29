<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use App\Models\KematianIkan;
use App\Models\Jurnal;
use App\Models\Pembelian;
use App\Models\Vendor;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Setup: Persediaan with Global Purchase but NO direct Kolam Link
$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Global']);
$bibit = Persediaan::firstOrCreate(['nama_barang' => 'Bibit Fallback Test'], ['kategori' => 'Bibit', 'satuan' => 'ekor']);

// Global Purchase (Stock exists, Price exists)
Pembelian::create([
    'persediaan_id' => $bibit->id,
    'vendor_id' => $vendor->id,
    'jumlah' => 10000,
    'harga_satuan' => 500, // Price is 500
    'total_harga' => 5000000,
    'tanggal_pembelian' => now()->subMonth(),
    'kolam_id' => null // Not linked to specific kolam
]);

// 2. Create Manual Kolam (No direct purchase link)
$kolam = Kolam::create([
    'nomor_kolam' => 111,
    'persediaan_id' => $bibit->id,
    'tanggal_tebar' => now(),
    'jumlah' => 1000,
    'status' => 'dalam_pemeliharaan'
]);

// 3. Trigger Kematian
$kematian = KematianIkan::create([
    'kolam_id' => $kolam->id,
    'jumlah_ekor' => 100,
    'tanggal' => now(),
    'catatan' => 'Test Fallback Logic'
]);

// 4. Check Journal
$jurnal = Jurnal::where('ref_type', KematianIkan::class)->where('ref_id', $kematian->id)->first();
if ($jurnal) {
    echo "[PASS] Jurnal Created with ID: " . $jurnal->id . "\n";
} else {
    echo "[FAIL] Jurnal NOT Created (Likely because Cost was 0).\n";
}

// Cleanup
$kematian->delete();
$kolam->delete();
// $bibit->delete();
