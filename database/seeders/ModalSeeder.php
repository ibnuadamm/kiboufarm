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
        $modal->tambahModal(10000000);

        // 2. Setoran Modal Awal
        $setoran = SetoranModal::create([
            'jumlah' => 10000000, // Rp 10 juta
            'tanggal' => now(),
            'keterangan' => 'Modal Awal Usaha',
        ]);

        // 3. Jurnal Awal
        $coaKas = \App\Models\Coa::firstOrCreate(
            ['kode' => '111'],
            ['nama' => 'Kas', 'tipe' => 'Asset']
        );
        $coaModal = \App\Models\Coa::firstOrCreate(
            ['kode' => '311'],
            ['nama' => 'Modal', 'tipe' => 'Equity']
        );

        $jurnal = \App\Models\Jurnal::create([
            'tanggal' => $setoran->tanggal,
            'keterangan' => 'Setoran Modal Awal Usaha',
            'ref_type' => SetoranModal::class,
            'ref_id' => $setoran->id,
        ]);

        \App\Models\JurnalDetail::create([
            'jurnal_id' => $jurnal->id,
            'coa_id' => $coaKas->id,
            'debit' => 10000000,
            'kredit' => 0,
        ]);

        \App\Models\JurnalDetail::create([
            'jurnal_id' => $jurnal->id,
            'coa_id' => $coaModal->id,
            'debit' => 0,
            'kredit' => 10000000,
        ]);
    }
}
