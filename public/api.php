<?php

require dirname(__DIR__) . '/bootstrap.php';

init_public_base();

header('Content-Type: application/json; charset=utf-8');

$config = require ROOT_PATH . '/config/config.php';
$pdo = App\Database::getInstance()->connection();
$products = new App\ProductRepository($pdo);
$cart = new App\Cart($products);
$mailer = new App\OrderMailer($config['app']['mail_from']);
$handler = new App\ApiHandler($cart, $mailer, $config);

$input = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (json_decode(file_get_contents('php://input'), true) ?? $_POST)
    : $_GET;

if (!is_array($input)) {
    $input = [];
}

$action = $_GET['action'] ?? $_POST['action'] ?? ($input['action'] ?? null);

if (!$action || !is_string($action)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Параметр action обязателен.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$result = $handler->handle($action, $input);
$code = $result['code'] ?? ($result['ok'] ? 200 : 422);
unset($result['code']);
http_response_code($code);
echo json_encode($result, JSON_UNESCAPED_UNICODE);
