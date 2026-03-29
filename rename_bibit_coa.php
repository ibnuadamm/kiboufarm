<?php

use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Rename Persediaan Bibit -> Persediaan Ikan
$bibit = Akun::where('nama', 'Persediaan Bibit')->first();
if ($bibit) {
    $bibit->update(['nama' => 'Persediaan Ikan']);
    echo "Renamed 'Persediaan Bibit' to 'Persediaan Ikan'.\n";
} else {
    // If not found, maybe create or check if already named
    $ikan = Akun::where('nama', 'Persediaan Ikan')->first();
    if ($ikan) {
        echo "'Persediaan Ikan' already exists.\n";
    } else {
        Akun::create(['kode' => '1-102', 'nama' => 'Persediaan Ikan', 'tipe' => 'Asset']); // Using 1-102 which was Bibit
        echo "Created 'Persediaan Ikan'.\n";
    }
}
