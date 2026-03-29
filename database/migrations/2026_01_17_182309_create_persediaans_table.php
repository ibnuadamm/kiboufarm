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
        Schema::create('persediaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang')->unique();
            $table->enum('kategori', ['Pakan', 'Obat', 'Vitamin', 'Alat', 'Bibit', 'Lainnya']);
            $table->string('satuan');
            $table->integer('stok_masuk')->default(0); 
            $table->integer('stok_keluar')->default(0);
            $table->integer('stok_sisa')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persediaans');
    }
};
