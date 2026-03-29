<?php

namespace Database\Seeders;

use App\Models\Modal;
use App\Models\SetoranModal;
use Illuminate\Database\Seeder;

class ModalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Inisialisasi Modal (Singleton)
        $modal = Modal::getSingleton();

        // 2. Setoran Modal Awal
        SetoranModal::create([
            'jumlah' => 10000000, // Rp 10 juta
            'tanggal' => now(),
            'keterangan' => 'Modal Awal Usaha',
        ]);
    }
}
