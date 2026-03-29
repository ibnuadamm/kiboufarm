<?php

namespace App\Observers;

use App\Models\BiayaOperasional;
use App\Models\Modal;
use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;

class BiayaOperasionalObserver
{
    /**
     * Handle the BiayaOperasional "created" event.
     */
    public function created(BiayaOperasional $biaya): void
    {
        // 1. Kurangi Saldo Modal (Business Logic)
        $modal = Modal::getSingleton();
        $modal->kurangiModal($biaya->jumlah);

        // 2. Buat Jurnal Otomatis
        // 2. Buat Jurnal Otomatis
        
        // Mapping Category to Account Name
        $kategori = trim($biaya->kategori);
        $accountName = 'Beban Lain-lain'; // Default Fallback

        switch ($kategori) {
            case 'Listrik':
                $accountName = 'Beban Listrik';
                break;
            case 'Air':
                $accountName = 'Beban Air';
                break;
            case 'Gaji Karyawan':
                $accountName = 'Beban Gaji Pegawai';
                break;
            case 'Lainnya':
                $accountName = 'Beban Lain-lain';
                break;
            default:
                // If user somehow inputs something else, try to find an account with that name, 
                // or fallback to Beban Lain-lain
                $accountName = 'Beban Lain-lain'; 
                break;
        }

        $akunBeban = Akun::where('nama', $accountName)->first();
        $akunKas = Akun::where('nama', 'Kas')->first();

        if ($akunBeban && $akunKas) {
            $jurnal = Jurnal::create([
                'tanggal' => $biaya->tanggal,
                'keterangan' => 'Biaya Operasional: ' . $biaya->keterangan . ' (' . $biaya->kategori . ')',
                'ref_type' => BiayaOperasional::class,
                'ref_id' => $biaya->id,
            ]);

            // Debit Beban
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunBeban->id,
                'debit' => $biaya->jumlah,
                'kredit' => 0,
            ]);

            // Kredit Kas
            JurnalDetail::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunKas->id,
                'debit' => 0,
                'kredit' => $biaya->jumlah,
            ]);
        }
    }
}
