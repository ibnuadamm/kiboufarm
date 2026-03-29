<?php

use App\Models\BiayaOperasional;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\Modal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Testing Biaya Operasional Automation ---\n";

// Ensure Modal exists for validation
$modal = Modal::getSingleton();
if ($modal->saldo_modal < 1000000) {
    $modal->tambahModal(1000000); // Add dummy funds
}

// 1. Create Biaya 'Gaji Karyawan'
echo "Creating Biaya 'Gaji Karyawan' (Rp 500.000)...\n";
$biaya = BiayaOperasional::create([
    'keterangan' => 'Test Gaji Auto',
    'jumlah' => 500000,
    'tanggal' => date('Y-m-d'),
    'kategori' => 'Gaji Karyawan'
]);

// 2. Check Journal
$jurnal = Jurnal::where('ref_type', BiayaOperasional::class)->where('ref_id', $biaya->id)->first();

if ($jurnal) {
    echo "Journal Created! ID: {$jurnal->id}\n";
    $details = JurnalDetail::with('akun')->where('jurnal_id', $jurnal->id)->get();
    
    foreach ($details as $detail) {
        $type = $detail->debit > 0 ? 'DEBIT ' : 'KREDIT';
        $amount = $detail->debit > 0 ? $detail->debit : $detail->kredit;
        echo "  $type: {$detail->akun->nama} (Rp " . number_format($amount) . ")\n";
    }

    // Validation
    $debitExists = $details->contains(fn($d) => $d->debit > 0 && $d->akun->nama === 'Beban Gaji Pegawai');
    $creditExists = $details->contains(fn($d) => $d->kredit > 0 && $d->akun->nama === 'Kas');

    if ($debitExists && $creditExists) {
        echo "SUCCESS: Journal is correct (Debit Beban Gaji Pegawai / Credit Kas).\n";
    } else {
        echo "FAILED: Incorrect accounts used.\n";
    }
} else {
    echo "FAILED: No Journal entry found!\n";
}

// Cleanup
$biaya->delete();
if ($jurnal) {
    JurnalDetail::where('jurnal_id', $jurnal->id)->delete();
    $jurnal->delete();
}
