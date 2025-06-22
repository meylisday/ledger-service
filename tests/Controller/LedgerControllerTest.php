<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LedgerControllerTest extends WebTestCase
{
    public function testCreateLedger(): void
    {
        $client = static::createClient();

        $client->request('POST', '/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'currency' => 'USD'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseFormatSame('json');

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('USD', $data['currency']);
    }

    public function testCreateLedgerFailsWithInvalidCurrency(): void
    {
        $client = static::createClient();

        $client->request('POST', '/ledgers', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'currency' => 'X'
        ]));

        $this->assertResponseStatusCodeSame(400);
    }
}
