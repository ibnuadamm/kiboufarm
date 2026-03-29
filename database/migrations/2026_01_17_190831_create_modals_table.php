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
        // Tabel Modal (Singleton - hanya 1 record)
        Schema::create('modals', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo_modal', 15, 2)->default(0);
            $table->timestamps();
        });

        // Tabel Setoran Modal (Riwayat)
        Schema::create('setoran_modals', function (Blueprint $table) {
            $table->id();
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setoran_modals');
        Schema::dropIfExists('modals');
    }
};
