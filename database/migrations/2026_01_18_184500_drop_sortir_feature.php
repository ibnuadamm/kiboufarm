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
        Schema::dropIfExists('sortir_ikans');

        Schema::table('kolam', function (Blueprint $table) {
            $table->dropColumn(['sortir_penambahan', 'sortir_pengurangan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('sortir_ikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kolam_asal_id')->constrained('kolam')->cascadeOnDelete();
            $table->foreignId('kolam_tujuan_id')->constrained('kolam')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('ukuran_ikan_tujuan');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::table('kolam', function (Blueprint $table) {
            $table->integer('sortir_penambahan')->default(0)->after('jumlah_tebar');
            $table->integer('sortir_pengurangan')->default(0)->after('sortir_penambahan');
        });
    }
};
