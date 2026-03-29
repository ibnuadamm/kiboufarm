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
        Schema::create('sortir_ikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_asal_id')->constrained('kolam')->restrictOnDelete();
            $table->foreignId('kolam_tujuan_id')->constrained('kolam')->restrictOnDelete();
            $table->integer('jumlah');
            $table->string('ukuran_ikan_tujuan');
            $table->date('tanggal');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sortir_ikans');
    }
};
