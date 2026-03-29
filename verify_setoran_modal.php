<?php

use App\Models\Modal;
use App\Models\SetoranModal;
use App\Models\Jurnal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Check Initial Balance
$modal = Modal::getSingleton();
$initialBalance = $modal->saldo_modal;
echo "Initial Balance: " . number_format($initialBalance) . "\n";

// 2. Perform Deposit
$amount = 50000;
echo "Depositing: " . number_format($amount) . "\n";

$setoran = SetoranModal::create([
    'jumlah' => $amount,
    'tanggal' => now(),
    'keterangan' => 'Verify Script'
]);

// 3. Verify Balance Increased
$modal->refresh(); // Force reload from DB
echo "Current Balance: " . number_format($modal->saldo_modal) . "\n";

if ($modal->saldo_modal == ($initialBalance + $amount)) {
    echo "[PASS] Balance Increased.\n";
} else {
    echo "[FAIL] Balance NOT Increased. Diff: " . ($modal->saldo_modal - $initialBalance) . "\n";
}

// 4. Cleanup
$setoran->delete();
// Manually revert balance
$modal->kurangiModal($amount);
echo "Cleanup done.\n";
