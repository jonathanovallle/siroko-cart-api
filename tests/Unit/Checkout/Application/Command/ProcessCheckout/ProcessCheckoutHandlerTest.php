<?php

namespace App\Tests\Unit\Checkout\Application\Command\ProcessCheckout;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\CartItem;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\Money;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;
use App\Checkout\Application\Command\ProcessCheckout\ProcessCheckoutCommand;
use App\Checkout\Application\Command\ProcessCheckout\ProcessCheckoutHandler;
use App\Checkout\Domain\Repository\OrderRepositoryInterface;
use App\Checkout\Domain\Service\PaymentResult;
use App\Checkout\Domain\Service\PaymentServiceInterface;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProcessCheckoutHandlerTest extends TestCase
{
    private CartRepositoryInterface|MockObject $cartRepository;
    private OrderRepositoryInterface|MockObject $orderRepository;
    private PaymentServiceInterface|MockObject $paymentService;
    private ProcessCheckoutHandler $handler;

    protected function setUp(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->paymentService = $this->createMock(PaymentServiceInterface::class);
        
        $this->handler = new ProcessCheckoutHandler(
            $this->cartRepository,
            $this->orderRepository,
            $this->paymentService
        );
    }

    public function testCanProcessCheckoutSuccessfully(): void
    {
        $cartId = CartId::generate();
        $cart = Cart::create();
        
        // Add an item to the cart
        $product = new Product(
            ProductId::generate(),
            'Test Product',
            'Description',
            Money::fromAmount(25.99),
            10
        );
        $cart->addItem($product, Quantity::fromInt(1));

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn($cart);

        $this->paymentService
            ->expects($this->once())
            ->method('processPayment')
            ->willReturn(PaymentResult::success('txn_123'));

        $this->orderRepository
            ->expects($this->once())
            ->method('save');

        $this->cartRepository
            ->expects($this->once())
            ->method('save')
            ->with($cart);

        $command = new ProcessCheckoutCommand(
            $cartId->toString(),
            'test@example.com',
            'John',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain',
            ['card_number' => '4242424242424242']
        );

        $orderId = $this->handler->handle($command);

        $this->assertIsString($orderId);
        $this->assertTrue($cart->isEmpty()); // Cart should be cleared after successful checkout
    }

    public function testThrowsExceptionWhenCartNotFound(): void
    {
        $cartId = CartId::generate();

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cart not found or empty');

        $command = new ProcessCheckoutCommand(
            $cartId->toString(),
            'test@example.com',
            'John',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain',
            ['card_number' => '4242424242424242']
        );

        $this->handler->handle($command);
    }

    public function testThrowsExceptionWhenCartIsEmpty(): void
    {
        $cartId = CartId::generate();
        $emptyCart = Cart::create();

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn($emptyCart);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cart not found or empty');

        $command = new ProcessCheckoutCommand(
            $cartId->toString(),
            'test@example.com',
            'John',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain',
            ['card_number' => '4242424242424242']
        );

        $this->handler->handle($command);
    }

    public function testThrowsExceptionWhenPaymentFails(): void
    {
        $cartId = CartId::generate();
        $cart = Cart::create();
        
        $product = new Product(
            ProductId::generate(),
            'Test Product',
            'Description',
            Money::fromAmount(25.99),
            10
        );
        $cart->addItem($product, Quantity::fromInt(1));

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn($cart);

        $this->paymentService
            ->expects($this->once())
            ->method('processPayment')
            ->willReturn(PaymentResult::failure('Payment declined'));

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Payment failed: Payment declined');

        $command = new ProcessCheckoutCommand(
            $cartId->toString(),
            'test@example.com',
            'John',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain',
            ['card_number' => '4242424242424242']
        );

        $this->handler->handle($command);
    }
}