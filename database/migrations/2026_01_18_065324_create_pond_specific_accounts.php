<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $accounts = [];

        // 1. Create Accounts for Kolam 1-10
        // Use logic 1-102-XX where XX is pond number
        for ($i = 1; $i <= 10; $i++) {
            $kode = '1-102-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $accounts[] = [
                'kode' => $kode,
                'nama' => "Persediaan Ikan Kolam $i",
                'tipe' => 'Asset',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 2. Create Beban Susut Ikan
        $accounts[] = [
            'kode' => '6-201',
            'nama' => 'Beban Susut Ikan',
            'tipe' => 'Expense',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach ($accounts as $acc) {
            $exists = DB::table('akuns')
                ->where('kode', $acc['kode'])
                ->orWhere('nama', $acc['nama'])
                ->exists();
            
            if (!$exists) {
                DB::table('akuns')->insert($acc);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional cleanup
    }
};
