<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Request to create a new ledger")]
class LedgerRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
    #[OA\Property(description: "Base currency of the ledger", example: "USD")]
    public string $currency;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->currency = strtoupper($data['currency'] ?? '');
        return $dto;
    }
}