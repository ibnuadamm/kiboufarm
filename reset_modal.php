<?php

use App\Models\Modal;
use App\Models\SetoranModal;
use App\Models\Jurnal;
use App\Models\JurnalDetail;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Resetting Modal History ---\n";

// 1. Delete all SetoranModal
$count = SetoranModal::count();
SetoranModal::truncate();
echo "Deleted $count SetoranModal records.\n";

// 2. Delete related Journals
// Journal ref_type = 'App\Models\SetoranModal'
$jurnals = Jurnal::where('ref_type', SetoranModal::class)->get();
$jurnalCount = $jurnals->count();
foreach ($jurnals as $j) {
    JurnalDetail::where('jurnal_id', $j->id)->delete();
    $j->delete();
}
echo "Deleted $jurnalCount related Journals.\n";

// 3. Reset Modal Balance
$modal = Modal::getSingleton();
$oldSaldo = $modal->saldo_modal;
$modal->saldo_modal = 0;
$modal->save();

echo "Reset Saldo Modal: Rp " . number_format($oldSaldo) . " -> Rp 0\n";
