<?php

use App\Models\Modals;
use App\Models\SetoranModal;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::transaction(function () {
    // 1. Clear Setoran Modal
    SetoranModal::truncate();
    echo "SetoranModal truncated.\n";

    // 2. Reset Master Modal Balance
    DB::table('modals')->update(['saldo_modal' => 0]);
    echo "Modal Balance reset to 0.\n";

    // 3. Clear Related Journals
    // Find Journals pointing to SetoranModal
    $journalIds = Jurnal::where('ref_type', 'App\Models\SetoranModal')->pluck('id');
    
    if ($journalIds->count() > 0) {
        JurnalDetail::whereIn('jurnal_id', $journalIds)->delete();
        Jurnal::whereIn('id', $journalIds)->delete();
        echo "Deleted " . $journalIds->count() . " related Jurnals.\n";
    } else {
        echo "No related Journals found.\n";
    }
});

echo "All Modal History Cleared.\n";
