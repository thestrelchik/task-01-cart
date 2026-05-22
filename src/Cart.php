<?php

declare(strict_types=1);

namespace App;

final class Cart
{
    private const SESSION_KEY = 'cart';

    public function __construct(
        private ProductRepository $products
    ) {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    public function getRaw(): array
    {
        return $_SESSION[self::SESSION_KEY];
    }

    public function getItemCount(): int
    {
        return array_sum($this->getRaw());
    }

    public function add(int $productId, int $quantity = 1): void
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Количество должно быть больше нуля.');
        }

        $this->ensureProductExists($productId);

        $cart = $this->getRaw();
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        $_SESSION[self::SESSION_KEY] = $cart;
    }

    public function setQuantity(int $productId, int $quantity): void
    {
        $this->ensureProductExists($productId);

        $cart = $this->getRaw();

        if ($quantity === 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $_SESSION[self::SESSION_KEY] = $cart;
    }

    public function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
    }

    public function buildView(): array
    {
        $cart = $this->getRaw();

        if ($cart === []) {
            return [
                'items' => [],
                'total_minor' => 0,
                'currency' => 'BYN',
                'item_count' => 0,
            ];
        }

        $products = $this->products->findByIds(array_map('intval', array_keys($cart)));
        $items = [];
        $totalMinor = 0;
        $currency = 'BYN';

        foreach ($cart as $productId => $quantity) {
            $productId = (int) $productId;
            $quantity = (int) $quantity;

            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];
            $lineMinor = (int) $product['price'] * $quantity;
            $totalMinor += $lineMinor;
            $currency = (string) $product['currency'];

            $items[] = [
                'id' => $productId,
                'title' => $product['title'],
                'price_minor' => (int) $product['price'],
                'price_formatted' => PriceFormatter::format((int) $product['price'], $product['currency']),
                'currency' => $product['currency'],
                'image_path' => public_url($product['image_path']),
                'quantity' => $quantity,
                'line_total_minor' => $lineMinor,
                'line_total_formatted' => PriceFormatter::format($lineMinor, $product['currency']),
            ];
        }

        return [
            'items' => $items,
            'total_minor' => $totalMinor,
            'total_formatted' => PriceFormatter::format($totalMinor, $currency),
            'currency' => $currency,
            'item_count' => $this->getItemCount(),
        ];
    }

    private function ensureProductExists(int $productId): void
    {
        if ($this->products->findById($productId) === null) {
            throw new \InvalidArgumentException('Товар не найден.');
        }
    }
}
