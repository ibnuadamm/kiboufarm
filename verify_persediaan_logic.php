<?php

use App\Models\Persediaan;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create Test Persediaan
$item = Persediaan::updateOrCreate(
    ['nama_barang' => 'Test Barang Logic'], 
    ['kategori' => 'Lainnya', 'satuan' => 'pcs', 'stok_masuk' => 100, 'stok_keluar' => 0]
);

echo "Initial: Masuk={$item->stok_masuk}, Keluar={$item->stok_keluar}, Sisa={$item->stok_sisa}\n";

// Test 1: Increase Out (Should reduce Sisa)
$item->stok_keluar = 20;
$item->save();
$item->refresh(); // Reload from DB
echo "After Keluar 20: Masuk={$item->stok_masuk}, Keluar={$item->stok_keluar}, Sisa={$item->stok_sisa} ... " . ($item->stok_sisa == 80 ? "PASS" : "FAIL") . "\n";

// Test 2: Increase In (Should increase Sisa)
$item->stok_masuk = 200;
$item->save();
$item->refresh();
echo "After Masuk 200: Masuk={$item->stok_masuk}, Keluar={$item->stok_keluar}, Sisa={$item->stok_sisa} ... " . ($item->stok_sisa == 180 ? "PASS" : "FAIL") . "\n";

// Cleanup
$item->delete();
