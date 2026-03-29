<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\DB::beginTransaction();

try {
    $log = "--- START VERIFICATION ---\n";

    // 1. Test Pakan Purchase
    $pakanId = 17;
    $vendorId = 11;
    $log .= "Simulating Pakan Purchase...\n";
    
    $pembelianPakan = \App\Models\Pembelian::create([
        'persediaan_id' => $pakanId,
        'vendor_id' => $vendorId,
        'jumlah' => 10,
        'harga_satuan' => 5000,
        'total_harga' => 50000,
        'tanggal_pembelian' => date('Y-m-d'),
        'user_id' => 1
    ]);

    $log .= "Pembelian Pakan created: ID {$pembelianPakan->id}\n";
    
    // Check Journal
    $jurnalPakan = \App\Models\Jurnal::where('ref_type', \App\Models\Pembelian::class)
        ->where('ref_id', $pembelianPakan->id)
        ->first();
        
    if ($jurnalPakan) {
        $log .= "[PASS] Journal created for Pakan.\n";
        foreach ($jurnalPakan->details as $detail) {
            $log .= " - Akun: {$detail->akun->nama} | D: {$detail->debit} | K: {$detail->kredit}\n";
        }
    } else {
        $log .= "[FAIL] No Journal for Pakan.\n";
    }

    $log .= "\n--------------------------------\n";

    // 2. Test Bibit Purchase for Kolam
    $bibitId = 16;
    $kolamId = 26;
    $kolam = \App\Models\Kolam::find($kolamId);
    $log .= "Simulating Bibit Purchase for Kolam {$kolam->nomor_kolam}...\n";

    $pembelianBibit = \App\Models\Pembelian::create([
        'persediaan_id' => $bibitId,
        'vendor_id' => $vendorId,
        'jumlah' => 1000,
        'harga_satuan' => 200,
        'total_harga' => 200000,
        'tanggal_pembelian' => date('Y-m-d'),
        'kolam_id' => $kolamId,
        'user_id' => 1
    ]);
    
    $log .= "Pembelian Bibit created: ID {$pembelianBibit->id}\n";

    // Check Journal
    $jurnalBibit = \App\Models\Jurnal::where('ref_type', \App\Models\Pembelian::class)
        ->where('ref_id', $pembelianBibit->id)
        ->with('details.akun')
        ->first();

    if ($jurnalBibit) {
        $log .= "[PASS] Journal created for Bibit.\n";
        foreach ($jurnalBibit->details as $detail) {
            $log .= " - Akun: {$detail->akun->nama} | D: {$detail->debit} | K: {$detail->kredit}\n";
        }
    } else {
        $log .= "[FAIL] No Journal for Bibit.\n";
    }

    // Rollback
    \Illuminate\Support\Facades\DB::rollBack();
    $log .= "\n--- ROLLED BACK (TEST MODE) ---\n";
    
    file_put_contents('verify_log.txt', $log);
    echo "Check verify_log.txt";

} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    file_put_contents('verify_log.txt', "ERROR: " . $e->getMessage());
    echo "Error.";
}
