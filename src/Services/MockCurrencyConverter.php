<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class MockCurrencyConverter
{
    private array $rates = [
        'USD' => [
            'EUR' => 0.9,
            'USD' => 1.0,
        ],
        'EUR' => [
            'USD' => 1.1,
            'EUR' => 1.0,
        ],
    ];

    public function convert(float $amount, string $from, string $to): float
    {
        if (!isset($this->rates[$from][$to])) {
            throw new InvalidArgumentException("Unsupported currency conversion from $from to $to");
        }

        return round($amount * $this->rates[$from][$to], 4);
    }
}