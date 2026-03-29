<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembelian;
use App\Models\Kolam;
use App\Models\Persediaan;
use App\Observers\PembelianObserver;

// 1. Get a Kolam and Bibit Persediaan
$kolam = Kolam::first();
$bibit = Persediaan::where('kategori', 'Bibit')->first();

if (!$kolam || !$bibit) {
    die("Data required not found (Kolam/Bibit).\n");
}

echo "Testing Kolam #{$kolam->nomor_kolam} (ID: {$kolam->id})\n";
echo "Initial Jumlah: {$kolam->jumlah}, Tebar: {$kolam->jumlah_tebar}\n";

// 2. Mock Purchase
$qty = 100;
$pembelian = new Pembelian();
$pembelian->id = 99999; // Fake ID
$pembelian->persediaan_id = $bibit->id;
$pembelian->kolam_id = $kolam->id;
$pembelian->jumlah = $qty;
$pembelian->total_harga = 100000;
$pembelian->tanggal_pembelian = now();

// 3. Trigger Observer Manually
// Note: We don't save $pembelian to avoid polluting DB, but Observer expects saved object normally.
// Observer uses $pembelian->persediaan which requires relation.
// relation won't work on non-saved object unless manually set or mocked.
// Alternative: Create Real Purchase and then Delete it.

echo "Creating Real Test Purchase...\n";
\Illuminate\Support\Facades\DB::beginTransaction();

try {
    $pembelian = Pembelian::create([
        'persediaan_id' => $bibit->id,
        'kolam_id' => $kolam->id,
        'jumlah' => $qty,
        'total_harga' => 100000,
        'tanggal_pembelian' => now(),
        'metode_pembayaran' => 'Tunai',
         'user_id' => 1 // ID User for now
    ]);
    
    // Observer fired automatically on create
    
    // Reload Kolam
    $kolam->refresh();
    echo "After Purchase:\n";
    echo "Jumlah: {$kolam->jumlah}, Tebar: {$kolam->jumlah_tebar}\n";
    
    if ($kolam->jumlah_tebar > 0) {
        echo "SUCCESS: Tebar incremented.\n";
    } else {
        echo "FAIL: Tebar not incremented.\n";
    }

    // Rollback to clean up
    \Illuminate\Support\Facades\DB::rollBack();
    echo "Transaction Rolled Back (Data Clean).\n";

} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
