<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Attempting to create table manually...\n";
    Schema::create('sortir_ikans_test', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kolam_asal_id')->constrained('kolam')->restrictOnDelete();
        $table->foreignId('kolam_tujuan_id')->constrained('kolam')->restrictOnDelete();
        $table->integer('jumlah');
        $table->string('ukuran_ikan_tujuan');
        $table->date('tanggal');
        $table->string('catatan')->nullable();
        $table->timestamps();
    });
    echo "Table 'sortir_ikans_test' created successfully!\n";
    Schema::dropIfExists('sortir_ikans_test');
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
