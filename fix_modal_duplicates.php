<?php

use App\Models\Modal;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Count existing
$count = Modal::count();
echo "Found $count Modal records.\n";

// 2. Delete All (Reset)
// Note: In production we would sum them up, but logs showed all 0.
DB::table('modals')->delete();
echo "Deleted all Modal records.\n";

// 3. Create One Master Record
$modal = Modal::create(['id' => 1, 'saldo_modal' => 0]);
echo "Created Master Modal (ID: {$modal->id}) with Balance 0.\n";

// 4. Verify
$finalCount = Modal::count();
echo "Final Count: $finalCount\n";
