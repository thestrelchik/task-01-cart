<?php

declare(strict_types=1);

namespace App;

final class OrderMailer
{
    public function __construct(private string $fromEmail) {}

    public function send(string $toEmail, string $customerName, array $cartView): bool
    {
        $subject = 'Состав заказа';
        $body = $this->buildBody($customerName, $toEmail, $cartView);
        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-type: text/plain; charset=UTF-8',
            'From: ' . $this->fromEmail,
        ]);

        return mail($toEmail, $subject, $body, $headers);
    }

    private function buildBody(string $name, string $email, array $cartView): string
    {
        $lines = [
            'Контакты:',
            'Имя: ' . $name,
            'Email: ' . $email,
            '',
            'Состав заказа:',
        ];

        foreach ($cartView['items'] as $item) {
            $lines[] = sprintf(
                '- %s x %d = %s',
                $item['title'],
                $item['quantity'],
                $item['line_total_formatted']
            );
        }

        $lines[] = '';
        $lines[] = 'Итого: ' . ($cartView['total_formatted'] ?? PriceFormatter::format(0, $cartView['currency']));

        return implode("\n", $lines);
    }
}
