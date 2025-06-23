<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

#[OA\Schema(description: "Transaction creation request")]
class TransactionRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[OA\Property(description: "Ledger UUID", example: "d290f1ee-6c54-4b01-90e6-d701748f0851")]
    public string $ledgerId;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[OA\Property(description: "Unique transaction identifier", example: "4e3c8a5d-4eae-11ec-81d3-0242ac130003")]
    public string $transactionId;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['debit', 'credit'], message: 'Type must be "debit" or "credit"')]
    #[OA\Property(description: "Transaction type: credit or debit", example: "credit")]
    public string $type;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[OA\Property(description: "Transaction amount", type: "string", example: "150.00")]
    public string $amount;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
    #[OA\Property(description: "Transaction currency", example: "USD")]

    public string $currency;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->ledgerId = $data['ledgerId'] ?? '';
        $dto->transactionId = $data['transactionId'] ?? '';
        $dto->type = strtolower($data['type'] ?? '');
        $dto->amount = (string) ($data['amount'] ?? '');
        $dto->currency = strtoupper($data['currency'] ?? '');
        return $dto;
    }
}