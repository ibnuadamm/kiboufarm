<?php

namespace Database\Seeders;

use App\Models\Pembelian;
use App\Models\Persediaan;
use Illuminate\Database\Seeder;

class PersediaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Persediaan
        $pakan = Persediaan::create([
            'nama_barang' => 'Pakan Lele A1',
            'kategori' => 'Pakan',
            'satuan' => 'Sak',
            // Stok 0 default
        ]);

        $obat = Persediaan::create([
            'nama_barang' => 'Vitamin Ikan',
            'kategori' => 'Vitamin',
            'satuan' => 'Botol',
        ]);

        // 2. Create Pembelian (should trigger updates)
        Pembelian::create([
            'persediaan_id' => $pakan->id,
            'jumlah' => 10,
            'harga_satuan' => 200000,
            'total_harga' => 2000000,
            'tanggal_pembelian' => now(),
        ]);

        Pembelian::create([
            'persediaan_id' => $obat->id,
            'jumlah' => 50,
            'harga_satuan' => 25000,
            'total_harga' => 1250000,
            'tanggal_pembelian' => now(),
        ]);
        
        // Manual check logic in seeder? No, just seed.
    }
}
