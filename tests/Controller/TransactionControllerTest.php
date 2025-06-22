<?php

namespace App\Tests\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TransactionControllerTest extends WebTestCase
{
    public function testCreateTransaction(): void
    {
        $client = static::createClient();

        $client->request('POST', '/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'currency' => 'USD'
        ]));
        $this->assertResponseIsSuccessful();
        $ledger = json_decode($client->getResponse()->getContent(), true);
        $ledgerId = $ledger['id'];

        $client->request('POST', '/transactions', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'ledgerId' => $ledgerId,
            'transactionId' => Uuid::uuid4()->toString(),
            'type' => 'credit',
            'amount' => 150.00,
            'currency' => 'USD'
        ]));

        $this->assertResponseIsSuccessful();
    }

    public function testCreateTransactionFailsWithBadType(): void
    {
        $client = static::createClient();

        $client->request('POST', '/transactions', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'ledgerId' => Uuid::uuid4()->toString(),
            'transactionId' => Uuid::uuid4()->toString(),
            'type' => 'transfer',
            'amount' => 100,
            'currency' => 'EUR'
        ]));

        $this->assertResponseStatusCodeSame(400);
    }
}
