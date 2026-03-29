<?php

use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$newAccounts = [
    ['kode' => '6-102', 'nama' => 'Beban Listrik', 'tipe' => 'Expense'],
    ['kode' => '6-103', 'nama' => 'Beban Air', 'tipe' => 'Expense'],
    ['kode' => '6-104', 'nama' => 'Beban Gaji', 'tipe' => 'Expense'],
    ['kode' => '6-105', 'nama' => 'Beban Lain-lain', 'tipe' => 'Expense'],
];

foreach ($newAccounts as $acc) {
    $exists = Akun::where('kode', $acc['kode'])->orWhere('nama', $acc['nama'])->exists();
    if (!$exists) {
        Akun::create($acc);
        echo "Created: " . $acc['nama'] . "\n";
    } else {
        echo "Skipped (Exists): " . $acc['nama'] . "\n";
    }
}
