<?php

use App\Models\Akun;
use App\Models\JurnalDetail;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n--- Refactoring COA ---\n";

// 1. Delete 'Beban Operasional'
$oldOp = Akun::where('nama', 'Beban Operasional')->first();
if ($oldOp) {
    echo "Deleting 'Beban Operasional'...\n";
    // Ideally check for usage, but user said "hapus", so we force or warn. 
    // We'll rename it to 'Beban Lain-lain' if it has usage to preserve history, or delete.
    // Let's safe-delete: if usage > 0, rename to 'Beban Lain-lain'.
    $usage = JurnalDetail::where('akun_id', $oldOp->id)->count();
    if ($usage > 0) {
        $oldOp->update(['nama' => 'Beban Lain-lain', 'kode' => '6-105']);
        echo "  > Renamed to 'Beban Lain-lain' because it had transactions.\n";
    } else {
        $oldOp->delete();
        echo "  > Deleted.\n";
    }
}

// 2. Create Specific Expense Accounts
$expenses = [
    ['kode' => '6-102', 'nama' => 'Beban Listrik', 'tipe' => 'Expense'],
    ['kode' => '6-103', 'nama' => 'Beban Air', 'tipe' => 'Expense'],
    ['kode' => '6-104', 'nama' => 'Beban Gaji', 'tipe' => 'Expense'],
    ['kode' => '6-105', 'nama' => 'Beban Lain-lain', 'tipe' => 'Expense'], // Might exist if renamed above
    ['kode' => '6-106', 'nama' => 'Susut Ikan', 'tipe' => 'Expense'],
];

foreach ($expenses as $acc) {
    if (!Akun::where('nama', $acc['nama'])->exists()) {
        Akun::create($acc);
        echo "Created: {$acc['nama']}\n";
    } else {
        echo "Exists: {$acc['nama']}\n";
    }
}

// 3. Delete 'Beban Susut Ikan' / 'Beban Kerugian Kematian Ikan' if empty
$oldSusut = Akun::whereIn('nama', ['Beban Susut Ikan', 'Beban Kerugian Kematian Ikan'])->get();
foreach ($oldSusut as $os) {
    if (JurnalDetail::where('akun_id', $os->id)->count() == 0) {
        $os->delete();
        echo "Deleted unused: {$os->nama}\n";
    }
}
