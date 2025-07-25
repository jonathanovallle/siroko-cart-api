<?php

namespace App\Tests\Unit\Checkout\Domain\ValueObject;

use App\Checkout\Domain\ValueObject\OrderStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function testCanCreateValidStatuses(): void
    {
        $pending = OrderStatus::pending();
        $paid = OrderStatus::paid();
        $shipped = OrderStatus::shipped();
        $cancelled = OrderStatus::cancelled();

        $this->assertEquals('pending', $pending->getValue());
        $this->assertEquals('paid', $paid->getValue());
        $this->assertEquals('shipped', $shipped->getValue());
        $this->assertEquals('cancelled', $cancelled->getValue());
    }

    public function testCanCheckStatusTypes(): void
    {
        $pending = OrderStatus::pending();
        $paid = OrderStatus::paid();
        $cancelled = OrderStatus::cancelled();

        $this->assertTrue($pending->isPending());
        $this->assertFalse($pending->isPaid());
        $this->assertFalse($pending->isCancelled());

        $this->assertTrue($paid->isPaid());
        $this->assertFalse($paid->isPending());

        $this->assertTrue($cancelled->isCancelled());
        $this->assertFalse($cancelled->isPaid());
    }

    public function testCanCreateFromString(): void
    {
        $status = OrderStatus::fromString('paid');
        
        $this->assertTrue($status->isPaid());
        $this->assertEquals('paid', $status->getValue());
    }

    public function testInvalidStatusThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid order status: invalid_status');
        
        OrderStatus::fromString('invalid_status');
    }

    public function testStatusEquality(): void
    {
        $status1 = OrderStatus::paid();
        $status2 = OrderStatus::paid();
        $status3 = OrderStatus::pending();

        $this->assertTrue($status1->equals($status2));
        $this->assertFalse($status1->equals($status3));
    }
}