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
        Schema::create('pemberian_pakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_id')->constrained('kolam')->cascadeOnDelete();
            $table->foreignId('persediaan_id')->constrained('persediaans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('jumlah');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemberian_pakans');
    }
};
