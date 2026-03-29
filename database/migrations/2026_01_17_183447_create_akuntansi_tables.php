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
        // Master Akun (COA)
        Schema::create('akuns', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // e.g., 1-1001
            $table->string('nama'); // e.g., Kas
            $table->string('tipe'); // e.g., Asset, Liability, Equity, Revenue, Expense
            $table->timestamps();
        });

        // Header Jurnal
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('keterangan');
            $table->nullableMorphs('ref'); // ref_type, ref_id (untuk link ke Pembelian/Penjualan logic)
            $table->timestamps();
        });

        // Detail Jurnal (Debit/Kredit)
        Schema::create('jurnal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('akun_id')->constrained('akuns')->restrictOnDelete();
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
        Schema::dropIfExists('akuns');
    }
};
