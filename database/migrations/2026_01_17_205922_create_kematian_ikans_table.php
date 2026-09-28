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
        Schema::create('kematian_ikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->integer('jumlah_ekor');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kematian_ikans');
    }
};
