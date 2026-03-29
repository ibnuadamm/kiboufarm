<?php

use App\Models\Persediaan;
use App\Models\Pembelian;
use App\Models\PemberianPakan;
use App\Models\KematianIkan;
use App\Models\Jurnal;
use App\Models\Vendor;
use App\Models\Kolam;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup Vendor & Kolam
$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Verification']);
$kolam = Kolam::firstOrCreate([
    'nomor_kolam' => 777,
    'status' => 'dalam_pemeliharaan'
], ['tanggal_tebar' => now(), 'jumlah' => 100]);

// TEST 1: Purchase Pakan -> Default Persediaan Pakan
echo "\n--- TEST 1: Purchase Pakan ---\n";
$pakan = Persediaan::create(['nama_barang' => 'Pakan Verify', 'kategori' => 'Pakan', 'satuan' => 'kg']);

$pembelian = Pembelian::create([
    'persediaan_id' => $pakan->id,
    'vendor_id' => $vendor->id,
    'jumlah' => 10,
    'harga_satuan' => 10000,
    'total_harga' => 100000,
    'tanggal_pembelian' => now()
]);

$jurnal = Jurnal::where('ref_type', Pembelian::class)->where('ref_id', $pembelian->id)->with('details.akun')->first();
if ($jurnal) {
    $debitName = $jurnal->details->where('debit', '>', 0)->first()->akun->nama;
    echo "Account Used: $debitName (Expected: Persediaan Pakan) ... " . ($debitName === 'Persediaan Pakan' ? "PASS" : "FAIL") . "\n";
} else { echo "[FAIL] No Journal\n"; }

// TEST 2: Feeding Pakan -> Credit Persediaan Pakan
echo "\n--- TEST 2: Feeding Pakan ---\n";
$feeding = PemberianPakan::create([
    'kolam_id' => $kolam->id,
    'persediaan_id' => $pakan->id, // Pakan
    'tanggal' => now(),
    'jumlah' => 5,
    'catatan' => 'Verify'
]);

$jurnalFeed = Jurnal::where('ref_type', PemberianPakan::class)->where('ref_id', $feeding->id)->with('details.akun')->first();
if ($jurnalFeed) {
    $creditName = $jurnalFeed->details->where('kredit', '>', 0)->first()->akun->nama;
    echo "Credit Account: $creditName (Expected: Persediaan Pakan) ... " . ($creditName === 'Persediaan Pakan' ? "PASS" : "FAIL") . "\n";
} else { echo "[FAIL] No Journal\n"; }

// TEST 3: Fish Death -> Credit Persediaan Bibit
echo "\n--- TEST 3: Fish Death ---\n";
$kematian = KematianIkan::create([
    'kolam_id' => $kolam->id,
    'jumlah_ekor' => 2,
    'tanggal' => now(),
    'catatan' => 'Verify'
]);

$jurnalDeath = Jurnal::where('ref_type', KematianIkan::class)->where('ref_id', $kematian->id)->with('details.akun')->first();
if ($jurnalDeath) {
    $creditName = $jurnalDeath->details->where('kredit', '>', 0)->first()->akun->nama;
    echo "Credit Account: $creditName (Expected: Persediaan Bibit) ... " . ($creditName === 'Persediaan Bibit' ? "PASS" : "FAIL") . "\n";
} else { echo "[FAIL] No Journal\n"; }

// Cleanup
if($jurnal) { $jurnal->details()->delete(); $jurnal->delete(); }
if($jurnalFeed) { $jurnalFeed->details()->delete(); $jurnalFeed->delete(); }
if($jurnalDeath) { $jurnalDeath->details()->delete(); $jurnalDeath->delete(); }

$pembelian->delete();
$feeding->delete();
$kematian->delete();
$pakan->delete();
