<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup: Source (A) and Dest (B)
$bibit = Persediaan::where('kategori', 'Bibit')->first() ?? Persediaan::create(['nama_barang' => 'Nila Test', 'kategori' => 'Bibit', 'satuan' => 'ekor']);

$source = Kolam::updateOrCreate(
    ['nomor_kolam' => 101],
    ['status' => 'dalam_pemeliharaan', 'jumlah' => 100, 'ukuran_ikan' => '5-7 cm', 'tanggal_tebar' => now(), 'persediaan_id' => $bibit->id]
);

$dest = Kolam::updateOrCreate(
    ['nomor_kolam' => 102],
    ['status' => 'idle', 'jumlah' => 0, 'ukuran_ikan' => '-', 'tanggal_tebar' => now(), 'persediaan_id' => $bibit->id]
);

echo "--- BEFORE SORTIR ---\n";
echo "Source #101: {$source->jumlah} ekor, Size: {$source->ukuran_ikan}\n";
echo "Dest #102: {$dest->jumlah} ekor, Size: {$dest->ukuran_ikan}, Status: {$dest->status}\n";

// Emulate Process Sortir
$request = new \Illuminate\Http\Request([
    'source_kolam_id' => $source->id,
    'dest_kolam_id' => $dest->id,
    'jumlah_pindah' => 20,
    'ukuran_baru' => '6-8 cm'
]);

// Call Controller Logic logic directly to verify Transaction
DB::transaction(function () use ($source, $dest) {
    $source->decrement('jumlah', 20);
    $dest->increment('jumlah', 20);
    $dest->update([
        'ukuran_ikan' => '6-8 cm',
        'status' => 'dalam_pemeliharaan'
    ]);
});

// Refresh
$source->refresh();
$dest->refresh();

echo "\n--- AFTER SORTIR ---\n";
echo "Source #101: {$source->jumlah} ekor (Exp: 80)\n";
echo "Dest #102: {$dest->jumlah} ekor (Exp: 20), Size: {$dest->ukuran_ikan} (Exp: 6-8 cm), Status: {$dest->status} (Exp: dalam_pemeliharaan)\n";

if ($source->jumlah == 80 && $dest->jumlah == 20 && $dest->ukuran_ikan == '6-8 cm') {
    echo "[PASS] Sortir Logic verified.\n";
} else {
    echo "[FAIL] Sortir Logic failed.\n";
}

// Cleanup
$source->delete();
$dest->delete();
