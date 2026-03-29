<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Akun;

return new class extends Migration
{
    public function up(): void
    {
        // 6-103 Beban Pakan
        if (!Akun::where('nama', 'Beban Pakan')->exists()) {
            Akun::create(['kode' => '6-103', 'nama' => 'Beban Pakan', 'tipe' => 'Expense']);
        }

        // 6-104 Beban Obat-Obatan
        if (!Akun::where('nama', 'Beban Obat-Obatan')->exists()) {
            Akun::create(['kode' => '6-104', 'nama' => 'Beban Obat-Obatan', 'tipe' => 'Expense']);
        }
    }

    public function down(): void
    {
        // Optional
    }
};
