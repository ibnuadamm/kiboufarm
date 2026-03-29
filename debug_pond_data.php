<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kolam;
use App\Models\Akun;

echo "--- CHECKING KOLAM ---\n";
$kolams = Kolam::all(['id', 'nomor_kolam']);
foreach ($kolams as $k) {
    echo "ID: {$k->id}, Nomor: {$k->nomor_kolam}\n";
}

echo "\n--- CHECKING AKUN PERSEDIAAN KOLAM ---\n";
$akuns = Akun::where('nama', 'like', 'Persediaan Ikan Kolam%')->get();
foreach ($akuns as $a) {
    echo "ID: {$a->id}, Nama: {$a->nama}, Kode: {$a->kode}\n";
}
