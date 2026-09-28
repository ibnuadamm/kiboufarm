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
        Schema::create('kolam', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_kolam');
            $table->foreignId('persediaan_id')
                ->constrained('persediaans')
                ->restrictOnDelete();
            $table->date('tanggal_tebar');
            $table->date('tanggal_panen')->nullable();
            $table->integer('jumlah');
            $table->integer('jumlah_mati')->default(0);
            $table->string('ukuran_ikan');
            $table->integer('jumlah_tebar')->default(0);
            $table->enum('status', [
                'masuk_bibit', 
                'dalam_pemeliharaan',
                'sortir',
                'panen'
            ])->default('masuk_bibit');
            $table->timestamps();

            $table->index('nomor_kolam');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kolam');
    }
};
