<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kolam', function (Blueprint $table) {
            $table->foreignId('persediaan_id')->nullable()->change();
            $table->date('tanggal_tebar')->nullable()->change();
            $table->string('ukuran_ikan')->nullable()->change();
            $table->integer('jumlah')->default(0)->change();
        });

        // Di MySQL, merubah enum paling aman adalah menggunakan DB::statement
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kolam MODIFY COLUMN status ENUM('kosong', 'masuk_bibit', 'dalam_pemeliharaan', 'sortir', 'panen') NOT NULL DEFAULT 'kosong'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kolam', function (Blueprint $table) {
            $table->foreignId('persediaan_id')->nullable(false)->change();
            $table->date('tanggal_tebar')->nullable(false)->change();
            $table->string('ukuran_ikan')->nullable(false)->change();
            $table->integer('jumlah')->change();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kolam MODIFY COLUMN status ENUM('masuk_bibit', 'dalam_pemeliharaan', 'sortir', 'panen') NOT NULL DEFAULT 'masuk_bibit'");
        }
    }
};
