<?php

use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Delete Generic 'Persediaan'
$generic = Akun::where('nama', 'Persediaan')->first();
if ($generic) {
    // Check if used? ideally we migrate usage, but for now just delete as requested.
    $generic->delete();
    echo "Deleted generic 'Persediaan' account.\n";
} else {
    echo "Generic 'Persediaan' account not found.\n";
}

// 2. Ensure Specific Expense Accounts
$expenses = [
    ['kode' => '6-103', 'nama' => 'Beban Pakan', 'tipe' => 'Expense'],
    ['kode' => '6-104', 'nama' => 'Beban Obat', 'tipe' => 'Expense'], // Make sure it's 'Beban Obat' not 'Beban Obat-Obatan' if user wants specific
    ['kode' => '6-105', 'nama' => 'Beban Vitamin', 'tipe' => 'Expense'],
];

foreach ($expenses as $exp) {
    // Update or Create
    $acc = Akun::where('kode', $exp['kode'])->first();
    if ($acc) {
        $acc->update(['nama' => $exp['nama']]); // Rename if needed (e.g. Obat-Obatan -> Obat)
        echo "Updated {$exp['nama']}\n";
    } else {
        // Check by Name to avoid duplicate if code different
        $accByName = Akun::where('nama', $exp['nama'])->first();
        if (!$accByName) {
            Akun::create($exp);
            echo "Created {$exp['nama']}\n";
        }
    }
}
