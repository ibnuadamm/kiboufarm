<?php

use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$accounts = [
    // Ensure Generic Persediaan (Maybe rename it to avoid confusion or keep as fallback?)
    // User wants "Pecah", so ideally we move value out. But for now let's just create the new ones.
    ['kode' => '1-102', 'nama' => 'Persediaan Bibit', 'tipe' => 'Asset'],
    ['kode' => '1-103', 'nama' => 'Persediaan Pakan', 'tipe' => 'Asset'],
    ['kode' => '1-104', 'nama' => 'Persediaan Obat', 'tipe' => 'Asset'],
    ['kode' => '1-105', 'nama' => 'Persediaan Vitamin', 'tipe' => 'Asset'],
];

foreach ($accounts as $acc) {
    // Check by Code first to avoid Unique Constraint Violation
    $existing = Akun::where('kode', $acc['kode'])->first();
    if ($existing) {
        $existing->update(['nama' => $acc['nama'], 'tipe' => $acc['tipe']]);
        echo "Updated: {$acc['nama']}\n";
    } else {
        // Check by Name
        $existingName = Akun::where('nama', $acc['nama'])->first();
        if ($existingName) {
            $existingName->update(['kode' => $acc['kode'], 'tipe' => $acc['tipe']]);
             echo "Updated Code: {$acc['nama']}\n";
        } else {
            Akun::create($acc);
            echo "Created: {$acc['nama']}\n";
        }
    }
}

// Rename generic 'Persediaan' to 'Persediaan (Umum/Lama)' or similar if needed?
// User said "nama akun persediaan cuman 1".
// Let's ensure the Specific ones definitely exist.
