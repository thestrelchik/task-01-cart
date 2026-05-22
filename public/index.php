<?php

require dirname(__DIR__) . '/bootstrap.php';

$config = require ROOT_PATH . '/config/config.php';
$pdo = App\Database::getInstance()->connection();
$productsRepo = new App\ProductRepository($pdo);
$cart = new App\Cart($productsRepo);
$products = $productsRepo->findAll();
$cartCount = $cart->getItemCount();
$cartQuantities = $cart->getRaw();
$appName = $config['app']['name'];
init_public_base();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appName) ?> — Каталог</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= public_url('/css/style.css') ?>">
</head>
<body>
<header class="site-header border-bottom mb-4">
    <section class="container py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h1 class="h4 mb-0"><?= htmlspecialchars($appName) ?></h1>
        <a href="<?= public_url('/cart.php') ?>" class="btn btn-outline-dark">Корзина (<span id="cart-count"><?= (int) $cartCount ?></span>)</a>
    </section>
</header>

<main class="container pb-5">
    <section class="row g-4" id="product-grid">
        <?php foreach ($products as $product): ?>
            <?php $inCartQty = (int) ($cartQuantities[(int) $product['id']] ?? 0); ?>
            <article class="col-12 col-sm-6 col-lg-3">
                <section class="card product-card h-100">
                    <section class="product-image-wrap">
                        <img src="<?= htmlspecialchars(public_url($product['image_path'])) ?>"
                             alt="<?= htmlspecialchars($product['title']) ?>"
                             class="product-image">
                    </section>
                    <section class="card-body d-flex flex-column">
                        <h2 class="h6 card-title"><?= htmlspecialchars($product['title']) ?></h2>
                        <p class="price mb-3"><?= App\PriceFormatter::format((int) $product['price'], $product['currency']) ?></p>
                        <section class="product-actions mt-auto" data-product-id="<?= (int) $product['id'] ?>">
                            <button type="button"
                                    class="btn btn-secondary w-100 add-to-cart<?= $inCartQty > 0 ? ' d-none' : '' ?>">
                                Добавить в корзину
                            </button>
                            <section class="catalog-qty-control d-flex align-items-center gap-2<?= $inCartQty > 0 ? '' : ' d-none' ?>">
                                <button type="button" class="btn btn-outline-secondary qty-minus" aria-label="Уменьшить">−</button>
                                <input type="number"
                                       class="form-control catalog-qty-input text-center"
                                       min="0"
                                       value="<?= $inCartQty ?>"
                                       aria-label="Количество">
                                <button type="button" class="btn btn-outline-secondary qty-plus" aria-label="Увеличить">+</button>
                            </section>
                        </section>
                    </section>
                </section>
            </article>
        <?php endforeach; ?>
    </section>

    <?php if (false): // отладка: список товаров из БД ?>
    <section class="db-preview mt-5 pt-4 border-top">
        <h2 class="h6 text-muted mb-3">Список товаров из БД (таблица products)</h2>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>id</th>
                        <th>title</th>
                        <th>price</th>
                        <th>currency</th>
                        <th>image_path</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= (int) $product['id'] ?></td>
                            <td><?= htmlspecialchars($product['title']) ?></td>
                            <td><?= (int) $product['price'] ?></td>
                            <td><?= htmlspecialchars($product['currency']) ?></td>
                            <td class="small"><?= htmlspecialchars($product['image_path']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>
</main>

<section class="toast-container position-fixed bottom-0 end-0 p-3">
    <section id="toast" class="toast" role="alert">
        <section class="toast-body"></section>
    </section>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (false): // отладка: console.log при загрузке каталога ?>
<script>
console.log('Товары из БД:', <?= json_encode($products, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);
console.log('Корзина в сессии:', <?= json_encode($cartQuantities, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);
</script>
<?php endif; ?>
<script src="<?= public_url('/js/catalog.js') ?>"></script>
</body>
</html>
