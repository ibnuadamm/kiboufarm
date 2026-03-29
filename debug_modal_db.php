<?php

use App\Models\Modal;
use App\Models\SetoranModal;
use App\Models\Jurnal;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- MODALS TABLE ---\n";
$modals = DB::table('modals')->get();
foreach ($modals as $m) {
    echo "ID: {$m->id} | Saldo: {$m->saldo_modal}\n";
}

echo "\n--- SETORAN MODALS TABLE (Last 5) ---\n";
$setorans = SetoranModal::latest()->take(5)->get();
foreach ($setorans as $s) {
    echo "ID: {$s->id} | Jml: {$s->jumlah} | Tgl: {$s->tanggal->format('Y-m-d')}\n";
}

echo "\n--- JURNALS (Setoran Type, Last 5) ---\n";
$jurnals = Jurnal::where('ref_type', SetoranModal::class)->latest()->take(5)->get();
foreach ($jurnals as $j) {
    echo "ID: {$j->id} | Ket: {$j->keterangan}\n";
}
