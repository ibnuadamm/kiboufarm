<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use App\Models\Penjualan;
use App\Models\Akun;
use App\Models\Modal;
use App\Models\Jurnal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Setup Accounts if missing
if (!Akun::where('nama', 'Kas')->exists()) Akun::create(['kode' => '1-100', 'nama' => 'Kas', 'tipe' => 'Asset']);
if (!Akun::where('nama', 'Pendapatan Penjualan')->exists()) Akun::create(['kode' => '4-101', 'nama' => 'Pendapatan Penjualan', 'tipe' => 'Revenue']);

// Setup Modal
$modal = Modal::getSingleton();
$initialModal = $modal->saldo_modal;
echo "Saldo Modal Awal: " . number_format($initialModal) . "\n";

// Setup Kolam (Active)
// Ensure bibit exists for foreign key
$bibit = Persediaan::firstOrCreate(['nama_barang' => 'Bibit Lele'], ['kategori' => 'Bibit']);
$kolam = Kolam::updateOrCreate(
    ['nomor_kolam' => 888], 
    [
        'persediaan_id' => $bibit->id,
        'tanggal_tebar' => now()->subMonths(3),
        'jumlah' => 1000,
        'ukuran_ikan' => '5-7 cm',
        'status' => 'dalam_pemeliharaan',
        'tanggal_panen' => null
    ]
);
echo "Kolam #888 setup (Status: " . $kolam->status . ")\n";

// 1. Create Penjualan (Per Ekor)
echo "\n--- TEST: CREATE PENJUALAN ---\n";
$jumlahEkor = 900;
$hargaPerEkor = 2000;
$totalHarga = $jumlahEkor * $hargaPerEkor; // 1,800,000

$penjualan = Penjualan::create([
    'kolam_id' => $kolam->id,
    'tanggal_penjualan' => now(),
    'pembeli' => 'Tengkulak Test',
    'jumlah_ekor' => $jumlahEkor,
    'harga_per_ekor' => $hargaPerEkor, // New field
    'total_harga' => $totalHarga,
    'catatan' => 'Test Panen Ekor'
]);

echo "Penjualan Created ID: " . $penjualan->id . " | Total: " . number_format($penjualan->total_harga) . "\n";

// 2. Verify Modal Increased
$modal->refresh();
echo "\n--- VERIFIKASI SALDO MODAL ---\n";
$expectedModal = $initialModal + $totalHarga;
echo "Saldo Akhir: " . number_format($modal->saldo_modal) . " (Expected: " . number_format($expectedModal) . ") ... ";
if ($modal->saldo_modal == $expectedModal) echo "[PASS]\n"; else echo "[FAIL]\n";

// 3. Verify Journal Created
echo "\n--- VERIFIKASI JURNAL ---\n";
$jurnal = Jurnal::where('ref_type', Penjualan::class)->where('ref_id', $penjualan->id)->with('details.akun')->first();
if ($jurnal) {
    echo "Jurnal found ID: " . $jurnal->id . "\n";
    foreach ($jurnal->details as $detail) {
        $type = $detail->debit > 0 ? 'Debit' : 'Credit';
        $amount = $detail->debit > 0 ? $detail->debit : $detail->kredit;
        echo "- " . $detail->akun->nama . ": " . $type . " " . number_format($amount) . "\n";
    }
} else {
    echo "[FAIL] Jurnal Not Found!\n";
}

// 4. Verify Kolam Harvested
$kolam->refresh();
echo "\n--- VERIFIKASI STATUS KOLAM ---\n";
echo "Status Akhir: " . $kolam->status . " ... ";
if ($kolam->status === 'panen') echo "[PASS]\n"; else echo "[FAIL]\n";

// Cleanup
$penjualan->delete(); // might trigger other logic if observer handles deleted? (empty in observer)
if ($jurnal) {
    $jurnal->details()->delete();
    $jurnal->delete();
}
$kolam->delete();
// Revert modal manually just to be clean for next tests
$modal->saldo_modal = $initialModal;
$modal->save();

echo "\n--- SELESAI ---\n";
