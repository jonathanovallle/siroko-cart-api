<?php

namespace App\Tests\Unit\Cart\Domain\ValueObject;

use App\Cart\Domain\ValueObject\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testCanCreateMoneyFromAmount(): void
    {
        $money = Money::fromAmount(25.99);
        
        $this->assertEquals(25.99, $money->getAmount());
        $this->assertEquals(2599, $money->getCents());
        $this->assertEquals('EUR', $money->getCurrency());
    }

    public function testCanCreateMoneyFromCents(): void
    {
        $money = Money::fromCents(2599);
        
        $this->assertEquals(25.99, $money->getAmount());
        $this->assertEquals(2599, $money->getCents());
    }

    public function testCanAddMoney(): void
    {
        $money1 = Money::fromAmount(10.50);
        $money2 = Money::fromAmount(15.25);
        
        $result = $money1->add($money2);
        
        $this->assertEquals(25.75, $result->getAmount());
    }

    public function testCanMultiplyMoney(): void
    {
        $money = Money::fromAmount(10.50);
        
        $result = $money->multiply(3);
        
        $this->assertEquals(31.50, $result->getAmount());
    }

    public function testNegativeAmountThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be negative');
        
        Money::fromAmount(-10.00);
    }

    public function testAddingDifferentCurrenciesThrowsException(): void
    {
        $euros = Money::fromAmount(10.00, 'EUR');
        $dollars = Money::fromAmount(10.00, 'USD');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot operate on different currencies');
        
        $euros->add($dollars);
    }

    public function testMoneyEquality(): void
    {
        $money1 = Money::fromAmount(25.99);
        $money2 = Money::fromAmount(25.99);
        $money3 = Money::fromAmount(30.00);
        
        $this->assertTrue($money1->equals($money2));
        $this->assertFalse($money1->equals($money3));
    }
}