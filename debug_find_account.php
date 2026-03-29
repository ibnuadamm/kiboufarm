<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Akun;

$name = "Persediaan Ikan Kolam 9";
echo "Searching for: '$name'\n";
$acc = Akun::where('nama', $name)->first();

if ($acc) {
    echo "FOUND: ID {$acc->id}, Nama '{$acc->nama}'\n";
} else {
    echo "NOT FOUND.\n";
    // Try LIKE
    $like = Akun::where('nama', 'like', '%Kolam 9%')->get();
    echo "Similar accounts found: " . $like->count() . "\n";
    foreach ($like as $l) {
        echo "- '{$l->nama}'\n";
    }
}
