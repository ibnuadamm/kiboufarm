<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembelian;

$last = Pembelian::with(['persediaan', 'kolam'])->latest('id')->first();

if ($last) {
    echo "Last Pembelian ID: {$last->id}\n";
    echo "Item: {$last->persediaan->nama_barang} (Kategori: {$last->persediaan->kategori})\n";
    echo "Kolam ID: " . ($last->kolam_id ?? 'NULL') . "\n";
    if ($last->kolam) {
        echo "Kolam Nomor: {$last->kolam->nomor_kolam}\n";
    }
} else {
    echo "No Pembelian found.\n";
}
