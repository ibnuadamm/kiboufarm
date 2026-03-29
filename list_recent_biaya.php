<?php

use App\Models\BiayaOperasional;
use App\Models\Jurnal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Recent Biaya Transactions ---\n";

$biayas = BiayaOperasional::latest()->take(5)->get();

foreach ($biayas as $b) {
    echo "ID: {$b->id} | Date: {$b->tanggal} | Kategori: [{$b->kategori}] | Ket: {$b->keterangan}\n";
    
    $jurnal = Jurnal::where('ref_type', BiayaOperasional::class)->where('ref_id', $b->id)->first();
    if ($jurnal) {
        foreach ($jurnal->details as $d) {
            if ($d->debit > 0) {
                 echo "   Debit Account: {$d->akun->nama} (Rp " . number_format($d->debit) . ")\n";
            }
        }
    } else {
        echo "   [NO JOURNAL]\n";
    }
    echo "--------------------------------------------------\n";
}
