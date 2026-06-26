<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Public Path: " . public_path() . "\n";
echo "Public Path (fotos): " . public_path('fotos_de_perfil') . "\n";
echo "Exists: " . (file_exists(public_path('fotos_de_perfil')) ? 'Yes' : 'No') . "\n";
