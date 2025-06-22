<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TransactionRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $ledgerId;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $transactionId;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['debit', 'credit'], message: 'Type must be "debit" or "credit"')]
    public string $type;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $amount;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 3)]
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