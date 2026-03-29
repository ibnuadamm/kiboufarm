<?php

use App\Models\Pembelian;
use App\Models\Persediaan;
use App\Models\Vendor;
use App\Models\Jurnal;
use App\Models\Akun;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ensure Kas Account exists
if (!Akun::where('nama', 'Kas')->exists()) {
    Akun::create(['kode' => '1-100', 'nama' => 'Kas', 'tipe' => 'Asset']);
}

// Create Purchase
$vendor = Vendor::firstOrCreate(['nama' => 'Vendor Jurnal Test']);
$item = Persediaan::firstOrCreate(['nama_barang' => 'Barang Jurnal Test'], ['kategori' => 'Lainnya', 'satuan' => 'pcs']);

$pembelian = Pembelian::create([
    'persediaan_id' => $item->id,
    'vendor_id' => $vendor->id,
    'jumlah' => 10,
    'harga_satuan' => 5000,
    'total_harga' => 50000,
    'tanggal_pembelian' => now()
]);

// Check Journal
$jurnal = Jurnal::where('ref_type', Pembelian::class)->where('ref_id', $pembelian->id)->with('details.akun')->first();

if ($jurnal) {
    echo "Journal Created:\n";
    foreach ($jurnal->details as $detail) {
        $type = $detail->debit > 0 ? 'Debit' : 'Kredit';
        $amount = $detail->debit > 0 ? $detail->debit : $detail->kredit;
        echo "- {$detail->akun->nama}: $type " . number_format($amount) . "\n";
    }

    $creditDetail = $jurnal->details->where('kredit', '>', 0)->first();
    if ($creditDetail && $creditDetail->akun->nama === 'Kas') {
        echo "[PASS] Kredit adalah Kas.\n";
    } else {
        echo "[FAIL] Kredit bukan Kas (Found: " . ($creditDetail ? $creditDetail->akun->nama : 'None') . ")\n";
    }
} else {
    echo "[FAIL] Journal Not Created.\n";
}

// Cleanup
if ($jurnal) { $jurnal->details()->delete(); $jurnal->delete(); }
$pembelian->delete();
