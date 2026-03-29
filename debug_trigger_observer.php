<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pembelian;
use App\Observers\PembelianObserver;

$last = Pembelian::latest('id')->first();
if ($last) {
    try {
        echo "Triggering Observer for Purchase ID: {$last->id}\n";
        $observer = new PembelianObserver();
        $observer->created($last);
        echo "Observer executed successfully.\n";
    } catch (\Exception $e) {
        echo "ERROR CAUGHT:\n";
        echo $e->getMessage() . "\n";
        if (method_exists($e, 'getSql')) {
             echo "SQL: " . $e->getSql() . "\n";
        }
    }
} else {
    echo "No purchase found.\n";
}
