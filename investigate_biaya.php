<?php

use App\Models\BiayaOperasional;
use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Investigation Enhanced ---\n";

$last = BiayaOperasional::latest()->first();
if ($last) {
    echo "Last Biaya ID: {$last->id}\n";
    echo "  Keterangan: " . $last->keterangan . "\n";
    echo "  Kategori: '" . $last->kategori . "'\n";
    echo "  Length: " . strlen($last->kategori) . "\n";
    
    // Find Journal
    $jurnal = Jurnal::where('ref_type', BiayaOperasional::class)->where('ref_id', $last->id)->first();
    if ($jurnal) {
        echo "  Journal ID: {$jurnal->id}\n";
        foreach ($jurnal->details as $d) {
            echo "    - " . ($d->debit > 0 ? "Debit" : "Credit") . ": " . $d->akun->nama . " (Rp " . number_format($d->debit + $d->kredit) . ")\n";
        }
    } else {
        echo "  NO JOURNAL FOUND for this Biaya!\n";
    }
} else {
    echo "No Biaya Operasional found.\n";
}

echo "\nChecking Accounts:\n";
$names = ['Beban Listrik', 'Beban Air', 'Beban Gaji Pegawai', 'Beban Lain-lain'];
foreach($names as $n) {
    $exists = Akun::where('nama', $n)->exists();
    echo "  $n: " . ($exists ? "EXISTS" : "MISSING") . "\n";
}
