<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup
$bibit = Persediaan::where('kategori', 'Bibit')->first();
if (!$bibit) {
    echo "No Bibit found. Using Create.\n";
    $bibit = Persediaan::create(['nama_barang' => 'Nila Debug', 'kategori' => 'Bibit', 'satuan' => 'ekor']);
}

// Ensure Source and Dest exist
$source = Kolam::updateOrCreate(['nomor_kolam' => 901], [
    'persediaan_id' => $bibit->id,
    'jumlah' => 100,
    'status' => 'dalam_pemeliharaan',
    'ukuran_ikan' => '5-7 cm',
    'tanggal_tebar' => now()
]);
$dest = Kolam::updateOrCreate(['nomor_kolam' => 902], [
    'persediaan_id' => $bibit->id, // Assume same for now
    'jumlah' => 0,
    'status' => 'idle', // or custom status
    'ukuran_ikan' => '-',
    'tanggal_tebar' => now()
]);

echo "Initial State:\n";
echo "Source: {$source->jumlah}\n";
echo "Dest: {$dest->jumlah} | {$dest->ukuran_ikan}\n";

$jumlahPindah = 10;
$ukuranBaru = '9-10 cm';

try {
    echo "Moving $jumlahPindah ekor...\n";

    DB::transaction(function () use ($source, $dest, $jumlahPindah, $ukuranBaru) {
        // 1. Source
        $source->decrement('jumlah', $jumlahPindah);
        echo "Source decremented.\n"; 
        
        // 2. Dest Update
        $currentDestQty = $dest->jumlah; 
        $newQty = $currentDestQty + $jumlahPindah;
        $fishType = $source->persediaan_id;

        echo "Updating Dest: Qty {$currentDestQty}->{$newQty}, Size '{$ukuranBaru}', FishType {$fishType}\n";

        // Assign individually to debug
        $dest->ukuran_ikan = $ukuranBaru;
        $dest->status = 'dalam_pemeliharaan';
        $dest->persediaan_id = $fishType;
        $dest->jumlah = $newQty;
        
        $dest->save();
        echo "Save Completed.\n";
    });

} catch (\Throwable $e) {
    echo "ERROR CAUGHT: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

// Verification - Re-fetch from DB
$sourceFresh = Kolam::find($source->id);
$destFresh = Kolam::find($dest->id);

echo "Final State:\n";
echo "Source: {$sourceFresh->jumlah} (Exp: 90)\n";
echo "Dest: {$destFresh->jumlah} (Exp: 10) | Size: {$destFresh->ukuran_ikan} (Exp: $ukuranBaru)\n";

if ($destFresh->jumlah == 10) {
    echo "[PASS] Database Updated.\n";
} else {
    echo "[FAIL] Database NOT Updated.\n";
}

// Cleanup
$source->delete();
$dest->delete();
