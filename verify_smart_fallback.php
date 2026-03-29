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

$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Smart']);
$bibit = Persediaan::create(['nama_barang' => 'Bibit Smart ' . rand(100,999), 'kategori' => 'Bibit', 'satuan' => 'ekor']);

// Purchase 1: Jan 1 @ 1000
Pembelian::create([
    'persediaan_id' => $bibit->id,
    'vendor_id' => $vendor->id,
    'jumlah' => 1000,
    'harga_satuan' => 1000,
    'total_harga' => 1000000,
    'tanggal_pembelian' => '2026-01-01'
]);

// Purchase 2: Feb 1 @ 2000
Pembelian::create([
    'persediaan_id' => $bibit->id,
    'vendor_id' => $vendor->id,
    'jumlah' => 1000,
    'harga_satuan' => 2000,
    'total_harga' => 2000000,
    'tanggal_pembelian' => '2026-02-01'
]);

// Kolam Manual: Tebar Feb 2. Unlinked.
// Should match Purchase 2 (Nearest before tebar)
$kolam = Kolam::create([
    'nomor_kolam' => 991,
    'persediaan_id' => $bibit->id,
    'tanggal_tebar' => '2026-02-02',
    'jumlah' => 500,
    'status' => 'dalam_pemeliharaan'
]);

// Trigger Kematian: 10 ekor
// Expected Cost: 10 * 2000 = 20.000
$kematian = KematianIkan::create([
    'kolam_id' => $kolam->id,
    'jumlah_ekor' => 10,
    'tanggal' => now(),
    'catatan' => 'Test Smart Fallback'
]);

$jurnal = Jurnal::where('ref_type', KematianIkan::class)->where('ref_id', $kematian->id)->with('details')->first();

if ($jurnal) {
    $debit = $jurnal->details->where('debit', '>', 0)->first()->debit;
    echo "Journal Debit: $debit (Expected: 20000) ... " . ($debit == 20000 ? "PASS" : "FAIL (Got " . $debit . ")") . "\n";
} else {
    echo "[FAIL] No Journal\n";
}

// Cleanup
if ($jurnal) { $jurnal->details()->delete(); $jurnal->delete(); }
$kematian->delete();
$kolam->delete();
$bibit->delete(); // Cascades deletes purchases usually? No, manual delete needed.
\App\Models\Pembelian::where('persediaan_id', $bibit->id)->delete();
