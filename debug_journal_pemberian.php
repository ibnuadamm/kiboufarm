<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();


use App\Models\Akun;
use App\Models\Persediaan;
use App\Models\Pembelian;
use App\Models\PemberianPakan;


$log = "";

// 1. Check Accounts
$log .= "=== Checking Accounts ===\n";
$accounts = Akun::where('nama', 'like', 'Persediaan%')->get();
if ($accounts->isEmpty()) {
    $log .= "No 'Persediaan%' accounts found.\n";
}
foreach ($accounts as $account) {
    $log .= "Found Account: {$account->nama} (ID: {$account->id})\n";
}

// Check "Persediaan Ikan" specifically (for debit side)
$ikanAccount = Akun::where('nama', 'Persediaan Ikan')->first();
if ($ikanAccount) {
    $log .= "Found Account: Persediaan Ikan (ID: {$ikanAccount->id})\n";
} else {
    $log .= "MISSING Account: Persediaan Ikan\n";
}


// 2. Check a sample Persediaan (Feed/Meds)
$log .= "\n=== Checking Persediaan (Samples) ===\n";
$items = Persediaan::whereIn('kategori', ['Pakan', 'Obat', 'Vitamin'])->take(5)->get();

if ($items->isEmpty()) {
    $log .= "No Persediaan items found in Pakan/Obat/Vitamin categories.\n";
}

foreach ($items as $item) {
    $log .= "Item: {$item->nama_barang} (ID: {$item->id}, Kategori: {$item->kategori})\n";
    
    // Check Purchase History for this item
    $purchase = Pembelian::where('persediaan_id', $item->id)->latest('tanggal_pembelian')->first();
    if ($purchase) {
        $log .= "  - Latest Purchase: Date={$purchase->tanggal_pembelian}, Price={$purchase->total_harga}, Qty={$purchase->jumlah}\n";
        $cost = $purchase->jumlah > 0 ? $purchase->total_harga / $purchase->jumlah : 0;
        $log .= "  - Calculated Cost: {$cost}\n";
    } else {
        $log .= "  - No Purchase History found!\n";
    }
}

file_put_contents('debug_output.txt', $log);
echo "Debug finished. Check debug_output.txt\n";

