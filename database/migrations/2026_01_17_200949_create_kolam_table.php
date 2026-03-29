<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kolam', function (Blueprint $table) {
            $table->id();

            // Nomor kolam fisik
            $table->integer('nomor_kolam');

            // Jenis ikan (ambil dari master persediaan kategori Bibit)
            $table->foreignId('persediaan_id')
                ->constrained('persediaans')
                ->restrictOnDelete();

            // Tanggal siklus
            $table->date('tanggal_tebar');
            $table->date('tanggal_panen')->nullable();

            // Jumlah ikan awal tebar
            $table->integer('jumlah');

            // Ukuran ikan
            $table->string('ukuran_ikan');

            // Status siklus kolam
            $table->enum('status', [
                'masuk_bibit', 
                'dalam_pemeliharaan',
                'sortir',
                'panen'
            ])->default('masuk_bibit');

            $table->timestamps();

            // Index tambahan (opsional tapi bagus)
            $table->index('nomor_kolam');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kolam');
    }
};
