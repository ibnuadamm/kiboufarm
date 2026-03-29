<?php

use App\Models\BiayaOperasional;
use App\Models\Modal;
use App\Models\Jurnal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Initial State
$modal = Modal::getSingleton();
$initialBalance = $modal->saldo_modal;
echo "Initial Balance: " . number_format($initialBalance) . "\n";

// 2. Perform Expense
$amount = 10000;
echo "Creating Expense: " . number_format($amount) . "\n";

$biaya = BiayaOperasional::create([
    'keterangan' => 'Test Biaya Limit',
    'jumlah' => $amount,
    'tanggal' => now(),
    'kategori' => 'Testing'
]);

// 3. Verify Balance Reduced
$modal->refresh();
$expectedBalance = $initialBalance - $amount;
echo "Calculated Expected: " . number_format($expectedBalance) . "\n";
echo "Actual Balance: " . number_format($modal->saldo_modal) . "\n";

if ($modal->saldo_modal == $expectedBalance) {
    echo "[PASS] Modal Reduced Correctly.\n";
} else {
    echo "[FAIL] Modal Balance Mismatch.\n";
}

// 4. Verify Journal
$jurnal = Jurnal::where('ref_type', BiayaOperasional::class)->where('ref_id', $biaya->id)->with('details.akun')->first();

if ($jurnal) {
    echo "Journal Found:\n";
    foreach ($jurnal->details as $detail) {
        $type = $detail->debit > 0 ? 'Debit' : 'Kredit';
        echo "- {$detail->akun->nama}: $type " . number_format($detail->debit ?: $detail->kredit) . "\n";
    }

    $debitIsBeban = $jurnal->details->where('debit', '>', 0)->first()->akun->nama === 'Beban Operasional';
    
    // Check Credit is Kas?
    $creditDetail = $jurnal->details->where('kredit', '>', 0)->first();
    $creditIsKas = $creditDetail->akun->nama === 'Kas';

    if ($debitIsBeban && $creditIsKas) {
        echo "[PASS] Journal Accounts Correct (Dr Beban Op, Cr Kas).\n";
    } else {
        echo "[FAIL] Journal Accounts Incorrect.\n";
    }
} else {
    echo "[FAIL] No Journal Created.\n";
}

// Cleanup
if ($jurnal) { $jurnal->details()->delete(); $jurnal->delete(); }
$biaya->delete();
// Revert Modal Balance
$modal->tambahModal($amount);
echo "Cleanup: Reverted Modal Balance.\n";
