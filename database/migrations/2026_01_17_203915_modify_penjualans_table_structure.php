<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['jumlah_berat_kg', 'harga_per_kg']);
            $table->decimal('harga_per_ekor', 15, 2)->after('jumlah_ekor');
            // Ensure jumlah_ekor is required/default logic (it's already integer default 0 from create)
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->decimal('jumlah_berat_kg', 10, 2)->default(0);
            $table->decimal('harga_per_kg', 15, 2)->default(0); // Add default to avoid sql error on rollback
            $table->dropColumn('harga_per_ekor');
        });
    }
};
