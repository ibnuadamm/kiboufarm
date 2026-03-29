<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            
            // Link ke Kolam (Siklus) yang dipanen
            $table->foreignId('kolam_id')
                ->constrained('kolam')
                ->restrictOnDelete(); // Jangan hapus history panen sembarangan

            $table->date('tanggal_penjualan');
            $table->string('pembeli')->nullable(); // Nama tengkulak/pembeli
            
            $table->decimal('jumlah_berat_kg', 10, 2)->default(0); // Total berat
            $table->integer('jumlah_ekor')->default(0); // Estimasi ekor (opsional)
            
            $table->decimal('harga_per_kg', 15, 2);
            $table->decimal('total_harga', 15, 2);

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
