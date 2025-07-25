<?php

namespace App\Tests\Unit\Checkout\Domain\Entity;

use App\Cart\Domain\Entity\CartItem;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\Money;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;
use App\Checkout\Domain\Entity\Order;
use App\Checkout\Domain\ValueObject\CustomerInfo;
use App\Checkout\Domain\ValueObject\OrderStatus;
use DomainException;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private CustomerInfo $customerInfo;
    private array $cartItems;
    private Money $totalAmount;

    protected function setUp(): void
    {
        $this->customerInfo = new CustomerInfo(
            'test@example.com',
            'John',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain'
        );

        $this->cartItems = [
            new CartItem(
                ProductId::generate(),
                'Product 1',
                Money::fromAmount(25.99),
                Quantity::fromInt(2)
            )
        ];

        $this->totalAmount = Money::fromAmount(51.98);
    }

    public function testCanCreateOrderFromCart(): void
    {
        $cartId = CartId::generate();
        
        $order = Order::fromCart(
            $cartId,
            $this->customerInfo,
            $this->cartItems,
            $this->totalAmount
        );

        $this->assertEquals($cartId, $order->getCartId());
        $this->assertEquals($this->customerInfo, $order->getCustomerInfo());
        $this->assertTrue($order->getStatus()->isPending());
        $this->assertEquals($this->totalAmount, $order->getTotalAmount());
        $this->assertCount(1, $order->getItems());
    }

    public function testCanMarkOrderAsPaid(): void
    {
        $order = Order::fromCart(
            CartId::generate(),
            $this->customerInfo,
            $this->cartItems,
            $this->totalAmount
        );

        $order->markAsPaid();

        $this->assertTrue($order->getStatus()->isPaid());
    }

    public function testCannotMarkNonPendingOrderAsPaid(): void
    {
        $order = Order::fromCart(
            CartId::generate(),
            $this->customerInfo,
            $this->cartItems,
            $this->totalAmount
        );

        $order->markAsPaid(); // First payment
        
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Order must be pending to be marked as paid');
        
        $order->markAsPaid(); // Second payment should fail
    }

    public function testCanShipPaidOrder(): void
    {
        $order = Order::fromCart(
            CartId::generate(),
            $this->customerInfo,
            $this->cartItems,
            $this->totalAmount
        );

        $order->markAsPaid();
        $order->ship();

        $this->assertEquals(OrderStatus::SHIPPED, $order->getStatus()->getValue());
    }

    public function testCannotShipUnpaidOrder(): void
    {
        $order = Order::fromCart(
            CartId::generate(),
            $this->customerInfo,
            $this->cartItems,
            $this->totalAmount
        );

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Order must be paid to be shipped');
        
        $order->ship();
    }

    public function testCanCancelPendingOrder(): void
    {
        $order = Order::fromCart(
            CartId::generate(),
            $this->customerInfo,
            $this->cartItems,
            $this->totalAmount
        );

        $order->cancel();

        $this->assertTrue($order->getStatus()->isCancelled());
    }

    public function testCannotCancelShippedOrder(): void
    {
        $order = Order::fromCart(
            CartId::generate(),
            $this->customerInfo,
            $this->cartItems,
            $this->totalAmount
        );

        $order->markAsPaid();
        $order->ship();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cannot cancel shipped or delivered order');
        
        $order->cancel();
    }
}