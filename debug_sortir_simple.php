<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup (Clean/Fresh)
Kolam::where('nomor_kolam', 901)->delete();
Kolam::where('nomor_kolam', 902)->delete();

$bibit = Persediaan::where('kategori', 'Bibit')->firstOrFail();

$source = Kolam::create([
    'nomor_kolam' => 901,
    'persediaan_id' => $bibit->id,
    'jumlah' => 100,
    'status' => 'dalam_pemeliharaan',
    'ukuran_ikan' => '5-7 cm',
    'tanggal_tebar' => now()
]);

$dest = Kolam::create([
    'nomor_kolam' => 902,
    'persediaan_id' => $bibit->id,
    'jumlah' => 0,
    'status' => 'masuk_bibit', // Default migration value
    'ukuran_ikan' => '-',
    'tanggal_tebar' => now()
]);

echo "Start:\nSource: {$source->jumlah}\nDest: {$dest->jumlah}\n";

// 1. Decrement Source
echo "Step 1: Decrementing Source...\n";
$source->decrement('jumlah', 10);
echo "Source Decremented.\n";

// 2. Update Dest
echo "Step 2: Updating Dest...\n";
$dest->ukuran_ikan = '9-10 cm';
$dest->status = 'dalam_pemeliharaan';
$dest->persediaan_id = $source->persediaan_id;
$dest->jumlah = 10; // 0 + 10

try {
    $saved = $dest->save();
    echo "Dest Saved: " . ($saved ? 'YES' : 'NO') . "\n";
} catch (\Throwable $e) {
    echo "Save FAILED: " . $e->getMessage() . "\n";
}

// Check DB
$finalDest = Kolam::find($dest->id);
echo "Final Dest Status: {$finalDest->status}, Qty: {$finalDest->jumlah}, Size: {$finalDest->ukuran_ikan}\n";
