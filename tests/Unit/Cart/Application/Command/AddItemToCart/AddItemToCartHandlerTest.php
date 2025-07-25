<?php

namespace App\Tests\Unit\Cart\Application\Command\AddItemToCart;

use App\Cart\Application\Command\AddItemToCart\AddItemToCartCommand;
use App\Cart\Application\Command\AddItemToCart\AddItemToCartHandler;
use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\Repository\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\Money;
use App\Cart\Domain\ValueObject\ProductId;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddItemToCartHandlerTest extends TestCase
{
    private CartRepositoryInterface|MockObject $cartRepository;
    private ProductRepositoryInterface|MockObject $productRepository;
    private AddItemToCartHandler $handler;

    protected function setUp(): void
    {
        $this->cartRepository = $this->createMock(CartRepositoryInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->handler = new AddItemToCartHandler(
            $this->cartRepository,
            $this->productRepository
        );
    }

    public function testCanAddItemToCart(): void
    {
        $cartId = CartId::generate();
        $productId = ProductId::generate();
        $cart = Cart::create();
        $product = new Product(
            $productId,
            'Test Product',
            'Description',
            Money::fromAmount(25.99),
            10
        );

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn($cart);

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn($product);

        $this->cartRepository
            ->expects($this->once())
            ->method('save')
            ->with($cart);

        $command = new AddItemToCartCommand(
            $cartId->toString(),
            $productId->toString(),
            2
        );

        $this->handler->handle($command);

        $this->assertFalse($cart->isEmpty());
        $this->assertEquals(2, $cart->getItemCount());
    }

    public function testThrowsExceptionWhenCartNotFound(): void
    {
        $cartId = CartId::generate();
        $productId = ProductId::generate();

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Cart not found');

        $command = new AddItemToCartCommand(
            $cartId->toString(),
            $productId->toString(),
            2
        );

        $this->handler->handle($command);
    }

    public function testThrowsExceptionWhenProductNotFound(): void
    {
        $cartId = CartId::generate();
        $productId = ProductId::generate();
        $cart = Cart::create();

        $this->cartRepository
            ->expects($this->once())
            ->method('findById')
            ->with($cartId)
            ->willReturn($cart);

        $this->productRepository
            ->expects($this->once())
            ->method('findById')
            ->with($productId)
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Product not found');

        $command = new AddItemToCartCommand(
            $cartId->toString(),
            $productId->toString(),
            2
        );

        $this->handler->handle($command);
    }
}