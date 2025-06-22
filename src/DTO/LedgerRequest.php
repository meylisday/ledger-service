<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LedgerRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
    public string $currency;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->currency = strtoupper($data['currency'] ?? '');
        return $dto;
    }
}