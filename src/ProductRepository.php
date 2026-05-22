<?php

declare(strict_types=1);

namespace App;

use PDO;

final class ProductRepository
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, title, price, currency, image_path FROM products ORDER BY id ASC'
        );

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, price, currency, image_path FROM products WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare(
            "SELECT id, title, price, currency, image_path FROM products WHERE id IN ($placeholders)"
        );
        $stmt->execute(array_values($ids));

        $products = [];
        foreach ($stmt->fetchAll() as $product) {
            $products[(int) $product['id']] = $product;
        }

        return $products;
    }
}
