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
        // Master COA
        Schema::create('coas', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('tipe');
            $table->timestamps();
        });

        // Header Jurnal
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('keterangan');
            $table->nullableMorphs('ref');
            $table->timestamps();
        });

        // Detail Jurnal (Debit/Kredit)
        Schema::create('jurnal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coa_id')->constrained('coas')->restrictOnDelete();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_details');
        Schema::dropIfExists('jurnals');
        Schema::dropIfExists('coas');
    }
};
