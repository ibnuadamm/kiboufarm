<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jurnal = \App\Models\Jurnal::with('details.akun')->find(115);
if ($jurnal) {
    echo "Journal #115 Date: {$jurnal->tanggal}\n";
    echo "Journal #115 Created At: {$jurnal->created_at}\n";

    // foreach removed
} else {
    echo "Journal #115 NOT FOUND.\n";
}

$latestPemberian = \App\Models\PemberianPakan::latest()->first();
echo "\nLatest Pemberian ID: {$latestPemberian->id}\n";
