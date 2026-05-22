# Тестовое задание Qmedia — корзина интернет-магазина

**Автор:** Стрельчик Андрей

## Задание

Простой интернет-магазин канцтоваров: каталог товаров из MySQL, корзина в сессии, изменение количества, оформление заказа с валидацией имени и email, отправка состава заказа на почту через `mail()`. Вся работа с корзиной и заказом — через один API-файл.

## Что сделано

- Каталог и корзина (PHP, Bootstrap 5, AJAX)
- Классы: `Database` (Singleton), `Cart`, `ProductRepository`, `Validator`, `OrderMailer`, `PriceFormatter`, `ApiHandler`
- API: `public/api.php` — `get_cart`, `add_to_cart`, `update_quantity`, `clear_cart`, `checkout`
- Товары в MySQL (8 позиций), цены в копейках, форматирование на выводе
- Дополнительно: счётчик в шапке, изменение количества на каталоге, кнопка «Очистить корзину»

## Ссылки

| | |
|---|---|
| **Демо (каталог)** | https://test-01.alwaysdata.net/index.php |
| **GitHub** | https://github.com/thestrelchik/test-01 |

## Локально

```bash
docker compose up -d --build
```

Сайт: http://localhost:8080/index.php

Настройки БД: `config/config.php` или переменные `DB_*` в `docker-compose.yml`.
