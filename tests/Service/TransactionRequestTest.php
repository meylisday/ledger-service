<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\DTO\TransactionRequest;
use PHPUnit\Framework\TestCase;

class TransactionRequestTest extends TestCase
{
    public function testFromArrayFillsProperties(): void
    {
        $data = [
            'ledgerId' => 'test-ledger-id',
            'transactionId' => 'test-tx-id',
            'type' => 'credit',
            'amount' => '100.00',
            'currency' => 'usd'
        ];

        $dto = TransactionRequest::fromArray($data);

        $this->assertEquals('test-ledger-id', $dto->ledgerId);
        $this->assertEquals('test-tx-id', $dto->transactionId);
        $this->assertEquals('credit', $dto->type);
        $this->assertEquals('100.00', $dto->amount);
        $this->assertEquals('USD', $dto->currency);
    }
}