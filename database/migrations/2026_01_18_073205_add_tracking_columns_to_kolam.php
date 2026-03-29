<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kolam', function (Blueprint $table) {
            $table->integer('jumlah_tebar')->default(0)->after('ukuran_ikan'); // Total Bibit Stocked (History)
            $table->integer('sortir_penambahan')->default(0)->after('jumlah_tebar');
            $table->integer('sortir_pengurangan')->default(0)->after('sortir_penambahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kolam', function (Blueprint $table) {
            $table->dropColumn(['jumlah_tebar', 'sortir_penambahan', 'sortir_pengurangan']);
        });
    }
};
