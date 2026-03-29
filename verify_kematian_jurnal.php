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

// 1. Setup Data: Bibit, Vendor, Pembelian, Kolam
$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Test']);
$bibit = Persediaan::firstOrCreate(['nama_barang' => 'Bibit Gold'], ['kategori' => 'Bibit', 'satuan' => 'ekor']);

// Create Purchase (To establish Cost)
$qty = 2000;
$price = 1000; // 1000 per ekor
$total = $qty * $price; // 2.000.000

// Manually trigger purchasing logic usually done via controller
// But here we need the LINK.
// Create Kolam first or simultaneously?
// Usually Controller Store -> Create Pembelian -> Create Kolam.
// Let's mimic:
$pembelian = Pembelian::create([
    'persediaan_id' => $bibit->id,
    'vendor_id' => $vendor->id,
    'jumlah' => $qty,
    'harga_satuan' => $price,
    'total_harga' => $total,
    'tanggal_pembelian' => now()
]);

// Create Kolam connected to this purchase (In real app, kolam is created by purchase, but here we manually link via ID query)
// Since Pembelian table HAS kolam_id, we must update it
$kolam = Kolam::create([
    'nomor_kolam' => 777,
    'persediaan_id' => $bibit->id,
    'tanggal_tebar' => now(),
    'jumlah' => $qty,
    'status' => 'masuk_bibit'
]);

$pembelian->kolam_id = $kolam->id;
$pembelian->save();

echo "Setup: Kolam #777, Pembelian Total=$total, Qty=$qty (Cost=$price/ekor)\n";

// 2. Trigger Kematian
$mati = 100;
echo "Action: Kematian $mati ekor...\n";
$kematian = KematianIkan::create([
    'kolam_id' => $kolam->id,
    'jumlah_ekor' => $mati,
    'tanggal' => now(),
    'catatan' => 'Test Jurnal'
]);

// 3. Verify Journal
$expectedLoss = $mati * $price; // 100 * 1000 = 100.000
echo "Expected Loss: " . number_format($expectedLoss) . "\n";

$jurnal = Jurnal::where('ref_type', KematianIkan::class)->where('ref_id', $kematian->id)->with('details.akun')->first();

if ($jurnal) {
    echo "Jurnal Found (ID: {$jurnal->id}):\n";
    foreach ($jurnal->details as $detail) {
        $type = $detail->debit > 0 ? 'Debit' : 'Kredit';
        $amount = $detail->debit > 0 ? $detail->debit : $detail->kredit;
        echo "- {$detail->akun->nama}: $type " . number_format($amount) . "\n";
    }
    
    // Check Value
    $debitDetail = $jurnal->details->where('debit', '>', 0)->first();
    if ($debitDetail->debit == $expectedLoss) {
        echo "[PASS] Nominal Jurnal Tepat.\n";
    } else {
        echo "[FAIL] Nominal Wrong. Got: " . $debitDetail->debit . "\n";
    }
} else {
    echo "[FAIL] Jurnal Not Created.\n";
}

// Cleanup
$kematian->delete();
if ($jurnal) { $jurnal->details()->delete(); $jurnal->delete(); }
$pembelian->delete();
$kolam->delete();

