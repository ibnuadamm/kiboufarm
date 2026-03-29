<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SetoranModal;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use App\Models\Modal;

// Ensure accounts exist (for test environment safety)
if (!Akun::where('nama', 'Kas')->exists()) {
    Akun::create(['kode' => '1-1001', 'nama' => 'Kas', 'tipe' => 'Asset']); // Adjusted code to avoid duplicate if seeded differently
}
if (!Akun::where('nama', 'Modal')->exists()) {
    Akun::create(['kode' => '3-1001', 'nama' => 'Modal', 'tipe' => 'Equity']);
}

// Ensure at least one Model instance exists or is handled
$modal = Modal::first();
if (!$modal) {
    Modal::create(['saldo_modal' => 0]);
}

echo "Initial Modal Saldo: " . Modal::getSingleton()->saldo_modal . "\n";

$jumlah = 1000000;
echo "Creating SetoranModal amount: " . $jumlah . "\n";

$setoran = SetoranModal::create([
    'jumlah' => $jumlah,
    'tanggal' => now(),
    'keterangan' => 'Test Setoran via Script'
]);

echo "Setoran Created: " . $setoran->id . "\n";
echo "New Modal Saldo: " . Modal::getSingleton()->saldo_modal . "\n";

$jurnal = Jurnal::where('ref_type', SetoranModal::class)->where('ref_id', $setoran->id)->first();

if ($jurnal) {
    echo "Jurnal found: " . $jurnal->id . "\n";
    $details = $jurnal->details;
    foreach ($details as $detail) {
        $akun = $detail->akun;
        echo "Detail: " . $akun->nama . " | Debit: " . $detail->debit . " | Kredit: " . $detail->kredit . "\n";
    }
} else {
    echo "JURNAL NOT FOUND!\n";
}
