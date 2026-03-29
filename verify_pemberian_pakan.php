<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use App\Models\Pembelian;
use App\Models\PemberianPakan;
use App\Models\Jurnal;
use App\Models\Vendor;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Setup Pakan with Purchase (To set Price)
$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Pakan']);
$pakan = Persediaan::create(['nama_barang' => 'Pakan Lele Test', 'kategori' => 'Pakan', 'satuan' => 'kg']);

// Purchase: 100kg @ 15,000 (Date: Jan 1)
Pembelian::create([
    'persediaan_id' => $pakan->id,
    'vendor_id' => $vendor->id,
    'jumlah' => 100,
    'harga_satuan' => 15000,
    'total_harga' => 1500000,
    'tanggal_pembelian' => '2026-01-01'
]);

// Kolam (Dummy)
$kolam = Kolam::first(); 
if (!$kolam) {
    $kolam = Kolam::create([
        'nomor_kolam' => 99,
        'persediaan_id' => $pakan->id, // dummy link
        'tanggal_tebar' => now(),
        'jumlah' => 100,
        'status' => 'dalam_pemeliharaan'
    ]);
}

$initialOut = $pakan->refresh()->stok_keluar;
echo "Initial Stok Keluar: $initialOut\n";

// 2. Input Pemberian Pakan (10 kg)
$usedQty = 10;
echo "Action: Feeding $usedQty kg...\n";

$pemberian = PemberianPakan::create([
    'kolam_id' => $kolam->id,
    'persediaan_id' => $pakan->id,
    'tanggal' => '2026-01-02', // After purchase
    'jumlah' => $usedQty,
    'catatan' => 'Test Feeding'
]);

// 3. Verify Stock Update
$pakan->refresh();
echo "Stok Keluar After: " . $pakan->stok_keluar . " (Expected: " . ($initialOut + $usedQty) . ") ... " . ($pakan->stok_keluar == $initialOut + $usedQty ? "PASS" : "FAIL") . "\n";

// 4. Verify Journal
$expectedCost = $usedQty * 15000; // 150,000
echo "Expected Cost: " . number_format($expectedCost) . "\n";

$jurnal = Jurnal::where('ref_type', PemberianPakan::class)->where('ref_id', $pemberian->id)->with('details.akun')->first();

if ($jurnal) {
    echo "Journal Found:\n";
    foreach ($jurnal->details as $detail) {
        $type = $detail->debit > 0 ? 'Debit' : 'Kredit';
        echo "- {$detail->akun->nama}: $type " . number_format($detail->debit ?: $detail->kredit) . "\n";
    }
    
    // Check Account Name 'Beban Pakan'
    $debitDetail = $jurnal->details->where('debit', '>', 0)->first();
    if ($debitDetail->akun->nama === 'Beban Pakan') {
        echo "[PASS] Account is Beban Pakan\n";
    } else {
        echo "[FAIL] Account is " . $debitDetail->akun->nama . "\n";
    }

    if ($debitDetail->debit == $expectedCost) {
        echo "[PASS] Cost Amount Correct\n";
    } else {
        echo "[FAIL] Cost Amount Wrong\n";
    }

} else {
    echo "[FAIL] Journal Not Created\n";
}

// Cleanup
if ($jurnal) { $jurnal->details()->delete(); $jurnal->delete(); }
$pemberian->delete();
$pakan->delete();
\App\Models\Pembelian::where('persediaan_id', $pakan->id)->delete();
