<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Services\MockCurrencyConverter;
use PHPUnit\Framework\TestCase;

class MockCurrencyConverterTest extends TestCase
{
    private MockCurrencyConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new MockCurrencyConverter();
    }

    public function testUsdToEurConversion(): void
    {
        $result = $this->converter->convert(100.0, 'USD', 'EUR');
        $this->assertEquals(90.0, $result);
    }

    public function testEurToUsdConversion(): void
    {
        $result = $this->converter->convert(100.0, 'EUR', 'USD');
        $this->assertEquals(110.0, $result);
    }

    public function testUnsupportedConversionThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->converter->convert(100.0, 'USD', 'GBP');
    }
}