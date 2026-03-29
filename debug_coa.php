<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$akuns = \App\Models\Akun::where('nama', 'LIKE', '%Persediaan%')->get();
$output = "FOUND ACCOUNTS:\n";
foreach ($akuns as $akun) {
    $output .= " - {$akun->nama} (Kode: {$akun->kode})\n";
}
file_put_contents('coa_check.txt', $output);
echo "Check done.";
echo "Done.";
