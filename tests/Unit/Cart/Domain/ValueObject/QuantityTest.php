<?php

namespace App\Tests\Unit\Cart\Domain\ValueObject;

use App\Cart\Domain\ValueObject\Quantity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QuantityTest extends TestCase
{
    public function testCanCreateQuantity(): void
    {
        $quantity = Quantity::fromInt(5);
        
        $this->assertEquals(5, $quantity->getValue());
        $this->assertFalse($quantity->isZero());
    }

    public function testCanCreateZeroQuantity(): void
    {
        $quantity = Quantity::fromInt(0);
        
        $this->assertEquals(0, $quantity->getValue());
        $this->assertTrue($quantity->isZero());
    }

    public function testCanAddQuantities(): void
    {
        $quantity1 = Quantity::fromInt(3);
        $quantity2 = Quantity::fromInt(2);
        
        $result = $quantity1->add($quantity2);
        
        $this->assertEquals(5, $result->getValue());
    }

    public function testCanSubtractQuantities(): void
    {
        $quantity1 = Quantity::fromInt(5);
        $quantity2 = Quantity::fromInt(2);
        
        $result = $quantity1->subtract($quantity2);
        
        $this->assertEquals(3, $result->getValue());
    }

    public function testNegativeQuantityThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quantity cannot be negative');
        
        Quantity::fromInt(-1);
    }

    public function testSubtractingMoreThanAvailableThrowsException(): void
    {
        $quantity1 = Quantity::fromInt(2);
        $quantity2 = Quantity::fromInt(5);
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot subtract more than available quantity');
        
        $quantity1->subtract($quantity2);
    }
}