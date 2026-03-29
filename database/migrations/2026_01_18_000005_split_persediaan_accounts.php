<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Akun;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Specific Persediaan Accounts
        $accounts = [
            ['kode' => '1-102', 'nama' => 'Persediaan Bibit', 'tipe' => 'Asset', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '1-103', 'nama' => 'Persediaan Pakan', 'tipe' => 'Asset', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '1-104', 'nama' => 'Persediaan Obat', 'tipe' => 'Asset', 'created_at' => now(), 'updated_at' => now()],
            ['kode' => '1-105', 'nama' => 'Persediaan Vitamin', 'tipe' => 'Asset', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($accounts as $acc) {
            $exists = \Illuminate\Support\Facades\DB::table('akuns')
                ->where('kode', $acc['kode'])
                ->orWhere('nama', $acc['nama'])
                ->exists();
            
            if (!$exists) {
                \Illuminate\Support\Facades\DB::table('akuns')->insert($acc);
            }
        }
    }

    public function down(): void
    {
        // Optional
    }
};
