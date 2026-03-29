<?php

use App\Models\Kolam;
use App\Models\Persediaan;
use App\Models\Pembelian;
use App\Models\Akun;
use App\Models\Modal;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Setup Data
echo "--- SEDANG MEMBUAT DATA DUMMY ---\n";
// Ensure Accounts Exist
if (!Akun::where('nama', 'Kas')->exists()) Akun::create(['kode' => '1-101', 'nama' => 'Kas', 'tipe' => 'Asset']);
if (!Akun::where('nama', 'Modal')->exists()) Akun::create(['kode' => '3-101', 'nama' => 'Modal', 'tipe' => 'Equity']);
if (!Akun::where('nama', 'Persediaan')->exists()) Akun::create(['kode' => '1-201', 'nama' => 'Persediaan', 'tipe' => 'Asset']);

// Ensure Modal exists
$modal = Modal::first();
if (!$modal) {
    Modal::create(['saldo_modal' => 5000000]); // 5 Jts
} else {
    $modal->update(['saldo_modal' => 5000000]); // Reset to 5 Jts
}
echo "Saldo Modal Awal: " . Modal::getSingleton()->saldo_modal . "\n";

// Create Kolam
$kolam = Kolam::create([
    'nama' => 'Kolam Test Otomasi',
    'lokasi' => 'Blok Test',
    'kapasitas' => 5000,
    'status' => 'Aktif',
    'total_bibit' => 0
]);
echo "Kolam dibuat: " . $kolam->nama . " | Bibit Awal: " . $kolam->total_bibit . "\n";

// Create Persediaan (Bibit)
$bibit = Persediaan::updateOrCreate(
    ['nama_barang' => 'Bibit Lele Sangkuriang Test'],
    [
        'kategori' => 'Bibit',
        'satuan' => 'Ekor',
        'stok_masuk' => 0,
        'stok_keluar' => 0,
        'stok_sisa' => 0
    ]
);
echo "Barang Bibit dibuat/direset: " . $bibit->nama_barang . "\n";

// 2. Simulate Purchase
echo "\n--- SIMULASI PEMBELIAN BIBIT ---\n";
$jumlahBeli = 1000;
$hargaSatuan = 500; // Total 500,000

$pembelian = Pembelian::create([
    'persediaan_id' => $bibit->id,
    'jumlah' => $jumlahBeli,
    'harga_satuan' => $hargaSatuan,
    'total_harga' => $jumlahBeli * $hargaSatuan,
    'tanggal_pembelian' => now(),
    'kolam_id' => $kolam->id // Select Kolam
]);

echo "Pembelian Berhasil ID: " . $pembelian->id . "\n";

// 3. Verify Results
echo "\n--- VERIFIKASI HASIL ---\n";

// Refetch data
$kolam->refresh();
$bibit->refresh();
$modal = Modal::getSingleton();

echo "1. Stok Persediaan: " . $bibit->stok_sisa . " (Expected: " . $jumlahBeli . ") ... ";
if ($bibit->stok_sisa == $jumlahBeli) echo "[OK]\n"; else echo "[FAIL]\n";

echo "2. Populasi Kolam: " . $kolam->total_bibit . " (Expected: " . $jumlahBeli . ") ... ";
if ($kolam->total_bibit == $jumlahBeli) echo "[OK]\n"; else echo "[FAIL]\n";

echo "3. Saldo Modal: " . $modal->saldo_modal . " (Expected: 4500000) ... ";
if ($modal->saldo_modal == 4500000.00) echo "[OK]\n"; else echo "[FAIL]\n";

// Clean up
$pembelian->delete(); // Observer might revert? usually manual revert needed for logic but deletion observer exists?
// Let's just leave it or force delete for cleanup if needed, but for test script is fine.
$kolam->delete();
$bibit->delete();

echo "\n--- SELESAI ---\n";
