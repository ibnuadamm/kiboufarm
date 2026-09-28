<?php

namespace Database\Seeders;

use App\Models\Coa;
use Illuminate\Database\Seeder;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coas = [
            ['kode' => '111', 'nama' => 'Kas', 'tipe' => 'Asset'],
            ['kode' => '1132', 'nama' => 'Persediaan Vitamin', 'tipe' => 'Asset'],
            ['kode' => '1133', 'nama' => 'Persediaan Obat', 'tipe' => 'Asset'],
            ['kode' => '1134', 'nama' => 'Persediaan Pakan', 'tipe' => 'Asset'],
            ['kode' => '1135', 'nama' => 'Persediaan Barang Jadi', 'tipe' => 'Asset'],
            ['kode' => '2-101', 'nama' => 'Hutang Usaha', 'tipe' => 'Liability'],
            ['kode' => '311', 'nama' => 'Modal', 'tipe' => 'Equity'],
            ['kode' => '5100', 'nama' => 'Harga Pokok Penjualan', 'tipe' => 'Expense'],
            ['kode' => '514', 'nama' => 'Beban Listrik', 'tipe' => 'Expense'],
            ['kode' => '515', 'nama' => 'Beban Air', 'tipe' => 'Expense'],
            ['kode' => '516', 'nama' => 'Beban Gaji Karyawan', 'tipe' => 'Expense'],
            ['kode' => '517', 'nama' => 'Beban Kematian Ikan', 'tipe' => 'Expense'],
            ['kode' => '6-105', 'nama' => 'Beban Lain-lain', 'tipe' => 'Expense'],
            ['kode' => '6-340', 'nama' => 'Beban Vitamin', 'tipe' => 'Expense'],
            ['kode' => '6-343', 'nama' => 'Beban Pakan', 'tipe' => 'Expense'],
        ];

        // Add pre-defined accounts for Kolam 1-10
        for ($i = 1; $i <= 10; $i++) {
            $coas[] = [
                'kode' => '1131' . $i,
                'nama' => "Persediaan Bibit Kolam $i",
                'tipe' => 'Asset',
            ];
        }

        foreach ($coas as $coa) {
            Coa::firstOrCreate(['kode' => $coa['kode']], $coa);
        }
    }
}
