<?php

namespace Tests\Unit\Traits;

use App\Traits\HasMoneyAccessors;
use PHPUnit\Framework\TestCase;

class HasMoneyAccessorsTest extends TestCase
{
    private $trait;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an anonymous class that uses the trait
        $this->trait = new class
        {
            use HasMoneyAccessors;

            // Make protected methods accessible for testing
            public function testCentsToBRL(int $cents): float
            {
                return $this->centsToBRL($cents);
            }

            public function testBrlToCents(float $brl): int
            {
                return $this->brlToCents($brl);
            }

            public function testFormatCentsAsBRL(int $cents): string
            {
                return $this->formatCentsAsBRL($cents);
            }
        };
    }

    /** @test */
    public function it_converts_cents_to_brl_correctly()
    {
        $this->assertEquals(100.00, $this->trait->testCentsToBRL(10000));
        $this->assertEquals(1.50, $this->trait->testCentsToBRL(150));
        $this->assertEquals(0.01, $this->trait->testCentsToBRL(1));
        $this->assertEquals(0.00, $this->trait->testCentsToBRL(0));
        $this->assertEquals(1234.56, $this->trait->testCentsToBRL(123456));
    }

    /** @test */
    public function it_converts_brl_to_cents_correctly()
    {
        $this->assertEquals(10000, $this->trait->testBrlToCents(100.00));
        $this->assertEquals(150, $this->trait->testBrlToCents(1.50));
        $this->assertEquals(1, $this->trait->testBrlToCents(0.01));
        $this->assertEquals(0, $this->trait->testBrlToCents(0.00));
        $this->assertEquals(123456, $this->trait->testBrlToCents(1234.56));
    }

    /** @test */
    public function it_handles_negative_values()
    {
        $this->assertEquals(-100.00, $this->trait->testCentsToBRL(-10000));
        $this->assertEquals(-10000, $this->trait->testBrlToCents(-100.00));
    }

    /** @test */
    public function it_rounds_correctly()
    {
        // Test rounding with precision
        $this->assertEquals(100.12, $this->trait->testCentsToBRL(10012));
        $this->assertEquals(10012, $this->trait->testBrlToCents(100.123)); // Should round to 10012
    }

    /** @test */
    public function it_formats_cents_as_brl_string()
    {
        $this->assertEquals('R$ 100,00', $this->trait->testFormatCentsAsBRL(10000));
        $this->assertEquals('R$ 1,50', $this->trait->testFormatCentsAsBRL(150));
        $this->assertEquals('R$ 1.234,56', $this->trait->testFormatCentsAsBRL(123456));
        $this->assertEquals('R$ 0,00', $this->trait->testFormatCentsAsBRL(0));
    }

    /** @test */
    public function it_maintains_precision_for_financial_calculations()
    {
        // Ensure no precision loss in conversion cycles
        $originalCents = 123456;
        $brl = $this->trait->testCentsToBRL($originalCents);
        $backToCents = $this->trait->testBrlToCents($brl);

        $this->assertEquals($originalCents, $backToCents);
    }
}
