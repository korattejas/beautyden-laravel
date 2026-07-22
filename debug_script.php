<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$variant = \App\Models\ServiceMasterVariant::first();
echo "Variant Columns:\n";
echo json_encode($variant->getAttributes(), JSON_PRETTY_PRINT);
