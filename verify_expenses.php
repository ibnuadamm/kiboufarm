<?php

use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$names = ['Beban Listrik', 'Beban Air', 'Beban Gaji', 'Beban Lain-lain'];
echo "\n--- Checking Expense Accounts ---\n";
foreach($names as $n) {
    echo str_pad($n, 20) . ': ' . (Akun::where('nama', $n)->exists() ? 'EXISTS' : 'MISSING') . "\n";
}
