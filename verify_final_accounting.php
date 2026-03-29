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

$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Final Test']);
$kolam = Kolam::firstOrCreate(['nomor_kolam' => 888, 'status' => 'dalam_pemeliharaan'], ['tanggal_tebar' => now(), 'jumlah' => 100]);

function checkJournal($refType, $refId, $expectedDebit, $expectedCredit, $desc) {
    echo "\nTesting: $desc\n";
    $jurnal = Jurnal::where('ref_type', $refType)->where('ref_id', $refId)->with('details.akun')->first();
    
    if (!$jurnal) {
        echo "[FAIL] No Journal Created.\n";
        return;
    }

    $debitAcct = $jurnal->details->where('debit', '>', 0)->first()->akun->nama ?? 'None';
    $creditAcct = $jurnal->details->where('kredit', '>', 0)->first()->akun->nama ?? 'None';

    echo "Dr: $debitAcct (Exp: $expectedDebit)\n";
    echo "Cr: $creditAcct (Exp: $expectedCredit)\n";

    if ($debitAcct === $expectedDebit && $creditAcct === $expectedCredit) {
        echo "[PASS] Logic Correct.\n";
    } else {
        echo "[FAIL] Account Mismatch.\n";
    }
}

// 1. Buy Vitamin
$vitamin = Persediaan::create(['nama_barang' => 'Vit Test', 'kategori' => 'Vitamin', 'satuan' => 'btl']);
$buyVit = Pembelian::create([
    'persediaan_id' => $vitamin->id, 'vendor_id' => $vendor->id, 'jumlah' => 5, 'harga_satuan' => 10000, 'total_harga' => 50000, 'tanggal_pembelian' => now()
]);
checkJournal(Pembelian::class, $buyVit->id, 'Persediaan Vitamin', 'Kas', 'Buy Vitamin');

// 2. Use Vitamin
$useVit = PemberianPakan::create([
    'kolam_id' => $kolam->id, 'persediaan_id' => $vitamin->id, 'tanggal' => now(), 'jumlah' => 1, 'catatan' => 'Vitamin Test'
]);
checkJournal(PemberianPakan::class, $useVit->id, 'Beban Vitamin', 'Persediaan Vitamin', 'Use Vitamin');

// 3. Buy Obat
$obat = Persediaan::create(['nama_barang' => 'Obat Test', 'kategori' => 'Obat', 'satuan' => 'btl']);
$buyObat = Pembelian::create([
    'persediaan_id' => $obat->id, 'vendor_id' => $vendor->id, 'jumlah' => 5, 'harga_satuan' => 20000, 'total_harga' => 100000, 'tanggal_pembelian' => now()
]);
checkJournal(Pembelian::class, $buyObat->id, 'Persediaan Obat', 'Kas', 'Buy Obat');

// 4. Use Obat
$useObat = PemberianPakan::create([
    'kolam_id' => $kolam->id, 'persediaan_id' => $obat->id, 'tanggal' => now(), 'jumlah' => 1, 'catatan' => 'Obat Test'
]);
checkJournal(PemberianPakan::class, $useObat->id, 'Beban Obat', 'Persediaan Obat', 'Use Obat');

// 5. Fish Death
$death = KematianIkan::create([
    'kolam_id' => $kolam->id, 'jumlah_ekor' => 2, 'tanggal' => now(), 'catatan' => 'Die'
]);
checkJournal(KematianIkan::class, $death->id, 'Beban Kerugian Kematian Ikan', 'Persediaan Bibit', 'Fish Death');

// Cleanup
$buyVit->delete(); $useVit->delete(); $vitamin->delete();
$buyObat->delete(); $useObat->delete(); $obat->delete();
$death->delete();
