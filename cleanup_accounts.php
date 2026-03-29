<?php

use App\Models\Akun;
use App\Models\JurnalDetail;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$accountsToCheck = ['Beban Listrik', 'Beban Air', 'Beban Gaji', 'Beban Lain-lain'];

echo "\n--- Cleaning Up Unused Accounts ---\n";

foreach ($accountsToCheck as $name) {
    $akun = Akun::where('nama', $name)->first();
    
    if ($akun) {
        $usageCount = JurnalDetail::where('akun_id', $akun->id)->count();
        
        if ($usageCount == 0) {
            $akun->delete();
            echo "[DELETED] $name (No transactions found)\n";
        } else {
            echo "[SKIPPED] $name (Has $usageCount transactions - Cannot delete safely)\n";
        }
    } else {
        echo "[NOT FOUND] $name\n";
    }
}
