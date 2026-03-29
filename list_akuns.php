<?php

use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$akuns = Akun::all();
foreach ($akuns as $akun) {
    echo "{$akun->kode} - {$akun->nama} ({$akun->tipe})\n";
}
