<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

echo "OK — PHP works in htdocs\n";
echo 'Time: ' . date('c') . "\n";

$publicIndex = __DIR__ . '/public/index.php';
echo 'public/index.php exists: ' . (is_file($publicIndex) ? 'yes' : 'NO') . "\n";
