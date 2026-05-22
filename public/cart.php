<?php

require dirname(__DIR__) . '/bootstrap.php';

$config = require ROOT_PATH . '/config/config.php';
$pdo = App\Database::getInstance()->connection();
$productsRepo = new App\ProductRepository($pdo);
$cart = new App\Cart($productsRepo);
init_public_base();
$cartView = $cart->buildView();
$appName = $config['app']['name'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName) ?> — Корзина</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= public_url('/css/style.css') ?>">
</head>
<body>
<header class="site-header border-bottom mb-4">
    <section class="container py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="h3 mb-0">Корзина</h1>
        <a href="<?= public_url('/index.php') ?>" class="btn btn-link">← К каталогу</a>
    </section>
</header>

<main class="container pb-5 cart-page">
    <section class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h2 class="h5 mb-0">Ваши товары</h2>
        <button type="button"
                id="clear-cart-btn"
                class="btn btn-outline-danger btn-sm<?= $cartView['items'] === [] ? ' d-none' : '' ?>">
            Очистить корзину
        </button>
    </section>

    <section id="cart-items" class="mb-4">
        <?php if ($cartView['items'] === []): ?>
            <p class="text-muted" id="empty-cart-message">Корзина пуста. <a href="<?= public_url('/index.php') ?>">Перейти к товарам</a></p>
        <?php else: ?>
            <?php foreach ($cartView['items'] as $item): ?>
                <article class="cart-item row g-3 align-items-center border-bottom py-3" data-product-id="<?= (int) $item['id'] ?>">
                    <section class="col-auto">
                        <img src="<?= htmlspecialchars(public_url($item['image_path'])) ?>"
                             alt="<?= htmlspecialchars($item['title']) ?>"
                             class="cart-item-image">
                    </section>
                    <section class="col">
                        <h3 class="h6 mb-1"><?= htmlspecialchars($item['title']) ?></h3>
                        <p class="mb-1 text-muted">Цена: <span class="item-price"><?= htmlspecialchars($item['price_formatted']) ?></span></p>
                        <label class="d-inline-flex align-items-center gap-2">
                            Количество:
                            <input type="number"
                                   class="form-control form-control-sm quantity-input"
                                   min="0"
                                   value="<?= (int) $item['quantity'] ?>"
                                   data-product-id="<?= (int) $item['id'] ?>">
                        </label>
                        <p class="mb-0 mt-2">Стоимость: <strong class="line-total"><?= htmlspecialchars($item['line_total_formatted']) ?></strong></p>
                    </section>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <p class="fs-5 fw-bold mb-4">Итого: <span id="cart-total"><?= htmlspecialchars($cartView['total_formatted'] ?? '0,00 BYN') ?></span></p>

    <section class="checkout-form-wrap">
        <h2 class="h5 mb-3">Ваши контакты</h2>
        <form id="checkout-form" novalidate>
            <section class="mb-3">
                <label for="customer-name" class="form-label">Ваше имя</label>
                <input type="text"
                       class="form-control"
                       id="customer-name"
                       name="name"
                       autocomplete="name"
                       inputmode="text"
                       maxlength="100"
                       aria-describedby="name-error">
                <div class="invalid-feedback d-block" id="name-error"></div>
            </section>
            <section class="mb-4">
                <label for="customer-email" class="form-label">Ваш email</label>
                <input type="email"
                       class="form-control"
                       id="customer-email"
                       name="email"
                       autocomplete="email"
                       inputmode="email"
                       maxlength="255"
                       aria-describedby="email-error">
                <div class="invalid-feedback d-block" id="email-error"></div>
            </section>
            <button type="submit" class="btn btn-secondary btn-lg rounded-pill px-4" id="checkout-btn">
                Оформить заказ
            </button>
            <p class="text-danger mt-2 d-none" id="checkout-error"></p>
            <p class="text-success mt-2 d-none" id="checkout-success"></p>
        </form>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= public_url('/js/cart.js') ?>"></script>
</body>
</html>
