<?php

declare(strict_types=1);

namespace App;

final class ApiHandler
{
    public function __construct(
        private Cart $cart,
        private OrderMailer $mailer,
        private array $config
    ) {}

    public function handle(string $action, array $input): array
    {
        return match ($action) {
            'get_cart' => $this->success($this->cart->buildView()),
            'add_to_cart' => $this->addToCart($input),
            'update_quantity' => $this->updateQuantity($input),
            'checkout' => $this->checkout($input),
            'clear_cart' => $this->clearCart(),
            default => $this->error('Неизвестное действие.', 400),
        };
    }

    private function addToCart(array $input): array
    {
        if (!isset($input['product_id'])) {
            return $this->error('Поле product_id обязательно.');
        }

        $quantity = isset($input['quantity']) ? (int) $input['quantity'] : 1;
        $error = Validator::validatePositiveInt($quantity, 'quantity');
        if ($error !== null || $quantity < 1) {
            return $this->error('quantity должно быть целым числом больше нуля.');
        }

        try {
            $this->cart->add((int) $input['product_id'], $quantity);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage());
        }

        return $this->success($this->cart->buildView());
    }

    private function updateQuantity(array $input): array
    {
        if (!isset($input['product_id'], $input['quantity'])) {
            return $this->error('Поля product_id и quantity обязательны.');
        }

        $error = Validator::validatePositiveInt($input['quantity'], 'quantity');
        if ($error !== null) {
            return $this->error($error);
        }

        try {
            $this->cart->setQuantity((int) $input['product_id'], (int) $input['quantity']);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage());
        }

        return $this->success($this->cart->buildView());
    }

    private function clearCart(): array
    {
        $this->cart->clear();

        return $this->success($this->cart->buildView());
    }

    private function checkout(array $input): array
    {
        if (!isset($input['name'], $input['email'])) {
            return $this->error('Поля name и email обязательны.');
        }

        $name = (string) $input['name'];
        $email = (string) $input['email'];

        $fieldErrors = [];
        $nameError = Validator::validateName($name);
        if ($nameError !== null) {
            $fieldErrors['name'] = $nameError;
        }

        $emailError = Validator::validateEmail($email);
        if ($emailError !== null) {
            $fieldErrors['email'] = $emailError;
        }

        if ($fieldErrors !== []) {
            return $this->fieldErrors($fieldErrors);
        }

        $cartView = $this->cart->buildView();
        if ($cartView['items'] === []) {
            return $this->error('Корзина пуста.');
        }

        if (!$this->mailer->send($email, $name, $cartView)) {
            return $this->error('Не удалось отправить письмо. Проверьте настройки mail() на сервере.', 500);
        }

        $this->cart->clear();

        return $this->success(['message' => 'Заказ оформлен. Письмо отправлено на ' . $email]);
    }

    private function success(array $data): array
    {
        return ['ok' => true, 'data' => $data];
    }

    private function error(string $message, int $code = 422): array
    {
        return ['ok' => false, 'error' => $message, 'code' => $code];
    }

    private function fieldErrors(array $errors, int $code = 422): array
    {
        return ['ok' => false, 'errors' => $errors, 'code' => $code];
    }
}
