<?php
declare(strict_types=1);

return [
        'db' => [
        'host' => 'mysql-test-01.alwaysdata.net',
        'port' => 3306,
        'name' => 'test-01_base',
        'user' => 'test-01_shop',
        'pass' => 'w3wb3z02042000',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'Тестовое задание Стрельчик Андрей',
        'mail_from' => getenv('MAIL_FROM') ?: 'noreply@localhost',
    ],
];
