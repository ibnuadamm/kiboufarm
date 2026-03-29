<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            // Drop ancient FK (pointing to 'kolams')
            // Trying constraint name from error message
            try {
                $table->dropForeign('pembelians_kolam_id_foreign');
            } catch (\Exception $e) {
                // Ignore if not found, or try array syntax
            }
        });

        Schema::table('pembelians', function (Blueprint $table) {
            // Re-add FK pointing to 'kolam'
            $table->foreign('kolam_id')
                  ->references('id')
                  ->on('kolam')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
             $table->dropForeign(['kolam_id']);
             // Revert logic would be guessing, let's leave valid structure
             $table->foreign('kolam_id')->references('id')->on('kolams')->nullOnDelete();
        });
    }
};
