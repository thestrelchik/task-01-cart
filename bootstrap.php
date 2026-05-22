<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);

require ROOT_PATH . '/src/public_paths.php';

$config = require ROOT_PATH . '/config/config.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $file = ROOT_PATH . '/src/' . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

App\Database::getInstance($config['db']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
