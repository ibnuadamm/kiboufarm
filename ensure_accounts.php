<?php

use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Refined list based on latest request
$accounts = [
    // Code | Name | Type
    ['kode' => '6-102', 'nama' => 'Beban Listrik', 'tipe' => 'Expense'],
    ['kode' => '6-103', 'nama' => 'Beban Air', 'tipe' => 'Expense'],
    ['kode' => '6-104', 'nama' => 'Beban Gaji Pegawai', 'tipe' => 'Expense'], // Updated name
    ['kode' => '6-105', 'nama' => 'Beban Lain-lain', 'tipe' => 'Expense'],
];

echo "\n--- Ensuring Accounts Exist ---\n";

foreach ($accounts as $acc) {
    // Check by Code first
    $existing = Akun::where('kode', $acc['kode'])->first();
    
    if ($existing) {
        // Update name if different (e.g. changing Beban Gaji -> Beban Gaji Pegawai)
        if ($existing->nama !== $acc['nama']) {
            echo "Updating {$existing->kode}: {$existing->nama} -> {$acc['nama']}\n";
            $existing->update(['nama' => $acc['nama']]);
        } else {
            echo "Exists: {$acc['nama']} ({$acc['kode']})\n";
        }
    } else {
        // Create new
        Akun::create($acc);
        echo "Created: {$acc['nama']} ({$acc['kode']})\n";
    }
}
