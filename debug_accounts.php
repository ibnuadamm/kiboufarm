<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Akun;

$akuns = Akun::all();
foreach ($akuns as $a) {
    echo "[{$a->id}] {$a->kode} - '{$a->nama}'\n";
}
