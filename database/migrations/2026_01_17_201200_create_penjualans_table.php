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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolam')->restrictOnDelete();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->nullOnDelete();
            $table->date('tanggal_penjualan');
            $table->string('pembeli')->nullable();
            $table->integer('jumlah_ekor')->default(0);
            $table->decimal('harga_per_ekor', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
