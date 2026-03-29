<?php

namespace Database\Seeders;

use App\Models\Akun;
use Illuminate\Database\Seeder;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akuns = [
            ['kode' => '1-101', 'nama' => 'Kas', 'tipe' => 'Asset'],
            ['kode' => '1-102', 'nama' => 'Bank', 'tipe' => 'Asset'],
            ['kode' => '1-201', 'nama' => 'Persediaan', 'tipe' => 'Asset'],
            ['kode' => '2-101', 'nama' => 'Hutang Usaha', 'tipe' => 'Liability'],
            ['kode' => '3-101', 'nama' => 'Modal', 'tipe' => 'Equity'],
            ['kode' => '4-101', 'nama' => 'Pendapatan Penjualan', 'tipe' => 'Revenue'],
            ['kode' => '5-101', 'nama' => 'Harga Pokok Penjualan', 'tipe' => 'Expense'],
            ['kode' => '6-101', 'nama' => 'Beban Operasional', 'tipe' => 'Expense'],
        ];

        foreach ($akuns as $akun) {
            Akun::create($akun);
        }
    }
}
