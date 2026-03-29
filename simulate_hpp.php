<?php

use App\Models\Kolam;
use App\Models\Pembelian;
use App\Models\PemberianPakan;
use App\Models\Penjualan;
use App\Models\Persediaan;
use App\Models\Jurnal;
use App\Models\Akun;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n--- SIMULASI PERHITUNGAN HPP ---\n";

DB::transaction(function () {
    // 0. Setup Data Dummy
    $vendor = \App\Models\Vendor::firstOrCreate(['nama' => 'Vendor Test', 'no_hp' => '081']);
    $pelanggan = \App\Models\Pelanggan::firstOrCreate(['nama' => 'Pelanggan Test', 'no_hp' => '082']);
    
    // Create Bibit & Pakan Items
    $bibit = Persediaan::firstOrCreate(
        ['nama_barang' => 'Bibit Nila Test'], 
        ['kategori' => 'Bibit', 'stok_masuk' => 0, 'stok_keluar' => 0, 'stok_sisa' => 0, 'satuan' => 'Ekor']
    );
    $pakan = Persediaan::firstOrCreate(
        ['nama_barang' => 'Pelet Test'], 
        ['kategori' => 'Pakan', 'stok_masuk' => 100, 'stok_keluar' => 0, 'stok_sisa' => 100, 'satuan' => 'Sak']
    );

    // 1. PEMBELIAN BIBIT (Tebar ke Kolam 99)
    $qtyBibit = 1000;
    $hargaBibit = 1000; // Rp 1.000/ekor
    $totalBibit = $qtyBibit * $hargaBibit; // Rp 1.000.000
    
    echo "\nSTEP 1: Beli Bibit $qtyBibit ekor @ Rp $hargaBibit\n";
    echo "Total Investasi Bibit: Rp " . number_format($totalBibit) . "\n";
    
    // We need to trigger the Controller logic essentially, or manually mimic it perfectly.
    // Let's create Kolam manual first and Pembelian manual to ensure clean test.
    
    $kolam = Kolam::create([
        'nomor_kolam' => 99,
        'persediaan_id' => $bibit->id,
        'tanggal_tebar' => now(),
        'jumlah' => 0, // Observer will increment
        'jumlah_tebar' => 0, // Observer will increment
        'ukuran_ikan' => '5 cm',
        'status' => 'dalam_pemeliharaan'
    ]);
    
    $pembelian = Pembelian::create([
        'persediaan_id' => $bibit->id,
        'kolam_id' => $kolam->id,
        'vendor_id' => $vendor->id,
        'user_id' => 1,
        'jumlah' => $qtyBibit,
        'harga_satuan' => $hargaBibit,
        'total_harga' => $totalBibit,
        'tanggal_pembelian' => now(),
    ]);
    
    $kolam->refresh();
    echo "Kolam #99 Populasi: {$kolam->jumlah} ekor\n";
    
    // 2. PEMBERIAN PAKAN
    $qtyPakan = 50; 
    // Assume price calculation logic or manually force expense
    // PemberianPakanObserver calculates cost based on Purchase history of Pakan.
    // Let's buy Pakan first to have a price.
    Pembelian::create([
        'persediaan_id' => $pakan->id,
        'vendor_id' => $vendor->id,
        'user_id' => 1,
        'jumlah' => 100,
        'harga_satuan' => 10000, // Rp 10.000/unit
        'total_harga' => 1000000,
        'tanggal_pembelian' => now()->subDay(),
    ]);
    
    $biayaPakan = $qtyPakan * 10000; // 50 * 10.000 = 500.000
    
    echo "\nSTEP 2: Beri Pakan $qtyPakan unit (Biaya est: Rp " . number_format($biayaPakan) . ")\n";
    
    $pemberian = PemberianPakan::create([
        'kolam_id' => $kolam->id,
        'persediaan_id' => $pakan->id,
        'tanggal' => now(),
        'jumlah' => $qtyPakan,
    ]);
    
    // 3. CEK TOTAL INVESTASI (Logic check before sale)
    $pondAccountName = "Persediaan Ikan Kolam 99";
    $akunPond = Akun::where('nama', $pondAccountName)->first();
    
    if (!$akunPond) {
        // Create if missing (Test env)
         $akunPond = Akun::create(['kode' => '1-102-99', 'nama' => $pondAccountName, 'tipe' => 'Asset']);
    }
    
    $debit = \App\Models\JurnalDetail::where('akun_id', $akunPond->id)->sum('debit');
    $kredit = \App\Models\JurnalDetail::where('akun_id', $akunPond->id)->sum('kredit');
    $netInvest = $debit - $kredit;
    
    echo "\n[CHECK] Total Akumulasi Biaya di Kolam 99 (via Jurnal):\n";
    echo "Debit: " . number_format($debit) . " (Bibit + Pakan)\n";
    echo "Kredit: " . number_format($kredit) . "\n";
    echo "Net Investasi: Rp " . number_format($netInvest) . "\n";
    echo "Expected: Rp 1.500.000 (1jt Bibit + 500rb Pakan)\n";
    
    // 4. PENJUALAN (Panen 500 ekor)
    $jualQty = 500;
    $hargaJual = 5000; // Untung banyak
    $totalJual = $jualQty * $hargaJual;
    
    echo "\nSTEP 3: Jual $jualQty ekor\n";
    
    $penjualan = Penjualan::create([
        'kolam_id' => $kolam->id,
        'pelanggan_id' => $pelanggan->id,
        'tanggal_penjualan' => now(),
        'jumlah_ekor' => $jualQty,
        'harga_per_ekor' => $hargaJual,
        'total_harga' => $totalJual,
        'pembeli' => 'Test Buyer'
    ]);
    
    // 5. HASIL PENJUALAN JURNAL HPP
    $jurnalHPP = Jurnal::where('ref_type', Penjualan::class)
        ->where('ref_id', $penjualan->id)
        ->where('keterangan', 'like', 'HPP%')
        ->first();
        
    if ($jurnalHPP) {
        $detailDr = $jurnalHPP->details()->where('debit', '>', 0)->first();
        $nilaiHPP = $detailDr->debit;
        
        echo "\n--- HASIL PERHITUNGAN SISTEM ---\n";
        echo "Nilai Jurnal HPP Terbentuk: Rp " . number_format($nilaiHPP) . "\n";
        
        // Manual Calc
        // HPP Per Ekor = Net Invest / (Sisa + Jual) = 1.500.000 / 1000 = 1.500
        // Total HPP = 1.500 * 500 = 750.000
        echo "Manual Calculation Check: (1.500.000 / 1000 ekor) * 500 ekor = Rp 750.000\n";
        
        if ($nilaiHPP == 750000) {
            echo "STATUS: VALID (Sesuai Rumus)\n";
        } else {
            echo "STATUS: INVALID (Periksa Logika)\n";
        }
    } else {
        echo "ERROR: Jurnal HPP tidak terbentuk.\n";
    }

    // Rollback DB so we don't spam
    DB::rollBack();
    echo "\n(Database rolled back, data bersih)\n";
});
