-- Только для локального MySQL / Docker (там можно CREATE DATABASE).
-- Для InfinityFree используйте install-infinityfree.sql
CREATE DATABASE IF NOT EXISTS qmedia_shop
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE qmedia_shop;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    price INT UNSIGNED NOT NULL COMMENT 'Price in minor units (kopecks)',
    currency VARCHAR(8) NOT NULL DEFAULT 'BYN',
    image_path VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET NAMES utf8mb4;

INSERT INTO products (title, price, currency, image_path) VALUES
('Тетрадь в клетку', 180, 'BYN', '/images/products/tetrad-12-listov-kletka-arkhangelsk.jpg'),
('Тетрадь в линейку', 195, 'BYN', '/images/products/tetrad-v-lineyky.jpg'),
('Блокнот-ежедневник', 1850, 'BYN', '/images/products/ejednevnik-bloknot.jpg'),
('Скетчбук', 2215, 'BYN', '/images/products/skechbook.jpg'),
('Ручка гелевая синяя', 220, 'BYN', '/images/products/ruchka-gelevaya.jpg'),
('Набор карандашей', 835, 'BYN', '/images/products/nabor-karandashey.jpg'),
('Блокнот в линейку', 300, 'BYN', '/images/products/bloknot-v-lineyky.jpg'),
('Простой карандаш', 35, 'BYN', '/images/products/prostoy-karandash.jpg');
