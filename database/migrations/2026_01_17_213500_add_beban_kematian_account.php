<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Akun;

return new class extends Migration
{
    public function up(): void
    {
        if (!Akun::where('nama', 'Beban Kerugian Kematian Ikan')->exists()) {
            Akun::create([
                'kode' => '6-102', // Assuming 6-101 is Beban Operasional
                'nama' => 'Beban Kerugian Kematian Ikan',
                'tipe' => 'Expense'
            ]);
        }
    }

    public function down(): void
    {
        // Optional
    }
};
