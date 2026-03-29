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
        // One-time fix for existing data
        DB::statement('UPDATE persediaans SET stok_sisa = stok_masuk - stok_keluar');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revert needed logic-wise
    }
};
