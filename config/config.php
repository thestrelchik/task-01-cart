<?php
declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'qmedia_shop',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: 'root',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'Тестовое задание Стрельчик Андрей',
        'mail_from' => getenv('MAIL_FROM') ?: 'noreply@localhost',
    ],
];
