<?php

use App\Models\SetoranModal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = SetoranModal::count();
echo "SetoranModal Count: $count\n";

if ($count > 0) {
    echo "Latest Records:\n";
    foreach (SetoranModal::latest()->take(5)->get() as $s) {
        echo "- ID: {$s->id} | {$s->tanggal->format('Y-m-d')} | Rp " . number_format($s->jumlah) . "\n";
    }
}
