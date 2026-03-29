<?php

use App\Models\Modal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modal = Modal::getSingleton();
echo "Start: " . $modal->saldo_modal . "\n";
$modal->tambahModal(100);
echo "After Direct: " . $modal->saldo_modal . "\n";
