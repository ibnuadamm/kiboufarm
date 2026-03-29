<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\KematianIkan;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup Persediaan & Kolam
$bibit = Persediaan::firstOrCreate(['nama_barang' => 'Bibit Lele V2'], ['kategori' => 'Bibit']);
$initialOut = $bibit->stok_keluar;
echo "Persediaan Initial Out: " . $initialOut . "\n";

$kolam = Kolam::updateOrCreate(
    ['nomor_kolam' => 999], 
    [
        'persediaan_id' => $bibit->id,
        'tanggal_tebar' => now(),
        'jumlah' => 1000, 
        'jumlah_mati' => 0,
        'ukuran_ikan' => '5-7 cm',
        'status' => 'dalam_pemeliharaan'
    ]
);
echo "Kolam #999 Initial: " . $kolam->jumlah . " (Mati: " . $kolam->jumlah_mati . ")\n";

// 1. Create Pelanggan
echo "\n--- TEST: PELANGGAN ---\n";
$pelanggan = Pelanggan::create([
    'nama' => 'Pak Budi',
    'tipe' => 'Tengkulak',
    'nomor_telepon' => '08123456789'
]);
echo "Pelanggan Created: " . $pelanggan->nama . " (ID: " . $pelanggan->id . ")\n";

// 2. Kematian Ikan Logic
echo "\n--- TEST: KEMATIAN IKAN ---\n";
$matiCount = 50;
$kematian = KematianIkan::create([
    'kolam_id' => $kolam->id,
    'jumlah_ekor' => $matiCount,
    'tanggal' => now(),
    'catatan' => 'Test Kematian Script'
]);

// Verify Kolam Updates
$kolam->refresh();
echo "Kolam After Kematian:\n";
echo "- Jumlah Hidup: " . $kolam->jumlah . " (Expected: 950) ... " . ($kolam->jumlah == 950 ? "PASS" : "FAIL") . "\n";
echo "- Jumlah Mati: " . $kolam->jumlah_mati . " (Expected: 50) ... " . ($kolam->jumlah_mati == 50 ? "PASS" : "FAIL") . "\n";

// Verify Persediaan Updates
$bibit->refresh();
echo "Persediaan After Kematian:\n";
echo "- Stok Keluar: " . $bibit->stok_keluar . " (Expected: " . ($initialOut + 50) . ") ... " . ($bibit->stok_keluar == $initialOut + 50 ? "PASS" : "FAIL") . "\n";

// 3. Penjualan with Pelanggan
echo "\n--- TEST: PENJUALAN WITH PELANGGAN ---\n";
$penjualan = Penjualan::create([
    'kolam_id' => $kolam->id,
    'pelanggan_id' => $pelanggan->id,
    'tanggal_penjualan' => now(),
    'jumlah_ekor' => 500,
    'harga_per_ekor' => 2000,
    'total_harga' => 1000000,
    'pembeli' => $pelanggan->nama // Should follow logic controller but we bypass here, manually set
]);

$checkPenjualan = Penjualan::find($penjualan->id);
echo "Penjualan Linked to Pelanggan ID: " . $checkPenjualan->pelanggan_id . " ... " . ($checkPenjualan->pelanggan_id == $pelanggan->id ? "PASS" : "FAIL") . "\n";

// Cleanup
$kematian->delete(); // This won't revert stats unless we add Deleted Observer logic (which we didn't yet). 
// So manual revert if needed, but for test script irrelevant.
$penjualan->delete();
$pelanggan->delete();
$kolam->delete();
// $bibit->delete(); // Keep bibit

echo "\n--- SELESAI ---\n";
