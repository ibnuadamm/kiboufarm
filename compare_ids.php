<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jurnal = \App\Models\Jurnal::find(115);
$latestPemberian = \App\Models\PemberianPakan::latest()->first();

echo "Journal 115 Ref ID: " . ($jurnal ? $jurnal->ref_id : 'NULL') . "\n";
echo "Latest Pemberian ID: " . ($latestPemberian ? $latestPemberian->id : 'NULL') . "\n";

if ($jurnal && $latestPemberian && $jurnal->ref_id == $latestPemberian->id) {
    echo "MATCH: Journal exists for latest Pemberian.\n";
} else {
    echo "MISMATCH: Journal missing for latest Pemberian (or Journal 115 is old).\n";
}
