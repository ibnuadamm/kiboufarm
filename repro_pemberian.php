<?php

use App\Models\Persediaan;
use App\Models\Pembelian;
use App\Models\PemberianPakan;
use App\Models\Kolam;
use App\Models\Jurnal;
use App\Models\Pemberian;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Starting Reproduction Test ===\n";

DB::beginTransaction();

try {
    // 1. Create Test Kolam
    $kolam = Kolam::first();
    if (!$kolam) {
        $kolam = Kolam::create([
            'nomor_kolam' => 99,
            'persediaan_id' => 1, // Dummy
            'tanggal_tebar' => now(),
            'jumlah' => 1000,
            'jumlah_tebar' => 1000,
            'status' => 'dalam_pemeliharaan'
        ]);
        echo "Created Test Kolam 99\n";
    }

    // 2. Create Test Persediaan (Pakan)
    $pakan = Persediaan::create([
        'nama_barang' => 'Test Pakan ' . uniqid(),
        'kategori' => 'Pakan',
        'satuan' => 'kg',
        'stok_masuk' => 0,
        'stok_keluar' => 0,
        'stok_sisa' => 0
    ]);
    echo "Created Persediaan: {$pakan->nama_barang} (ID: {$pakan->id})\n";

    // 3. Create Pembelian (to give stock and price)
    $pembelian = Pembelian::create([
        'persediaan_id' => $pakan->id,
        'tanggal_pembelian' => Carbon::now()->subDay(),
        'jumlah' => 100,
        'total_harga' => 500000, // 5000 per unit
        'harga_satuan' => 5000,
        'keterangan' => 'Test Purchase'
    ]);
    echo "Created Pembelian: Qty 100, Total 500000\n";
    
    // Refresh persediaan to see updated stock (Observer should handle this)
    $pakan->refresh();
    echo "Stock after purchase: {$pakan->stok_sisa}\n";

    // 4. Create PemberianPakan
    echo "Creating PemberianPakan...\n";
    $pemberian = PemberianPakan::create([
        'kolam_id' => $kolam->id,
        'persediaan_id' => $pakan->id,
        'tanggal' => Carbon::now(),
        'jumlah' => 10,
        'catatan' => 'Test Usage'
    ]);
    echo "Created PemberianPakan ID: {$pemberian->id}\n";

    // 5. Check Journal
    $jurnal = Jurnal::where('ref_type', PemberianPakan::class)
        ->where('ref_id', $pemberian->id)
        ->first();

    if ($jurnal) {
        echo "SUCCESS: Journal FOUND (ID: {$jurnal->id})\n";
        foreach ($jurnal->details as $detail) {
            echo "  - Account: {$detail->akun->nama}, Debit: {$detail->debit}, Kredit: {$detail->kredit}\n";
        }
    } else {
        echo "FAILURE: Journal NOT FOUND!\n";
    }

} catch (\Exception $e) {
    file_put_contents('repro_error.txt', $e->getMessage());
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
} finally {
    // DB::rollBack(); // Rollback so we don't junk the DB
    // Or commit if we want to see it
    DB::rollBack();
    echo "=== Test Finished (Rolled Back) ===\n";
}
