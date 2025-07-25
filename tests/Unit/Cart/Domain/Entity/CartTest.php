<?php

namespace App\Tests\Unit\Cart\Domain\Entity;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\ValueObject\Money;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    private Product $product;
    private Cart $cart;

    protected function setUp(): void
    {
        $this->product = new Product(
            ProductId::generate(),
            'Test Product',
            'Test Description',
            Money::fromAmount(25.99),
            10
        );

        $this->cart = Cart::create();
    }

    public function testCanCreateEmptyCart(): void
    {
        $this->assertTrue($this->cart->isEmpty());
        $this->assertEquals(0, $this->cart->getItemCount());
        $this->assertEquals(Money::fromCents(0), $this->cart->getTotalAmount());
    }

    public function testCanAddItemToCart(): void
    {
        $quantity = Quantity::fromInt(2);
        
        $this->cart->addItem($this->product, $quantity);
        
        $this->assertFalse($this->cart->isEmpty());
        $this->assertEquals(2, $this->cart->getItemCount());
        $this->assertTrue($this->cart->hasItem($this->product->getId()));
        
        $expectedTotal = Money::fromAmount(51.98); // 25.99 * 2
        $this->assertEquals($expectedTotal, $this->cart->getTotalAmount());
    }

    public function testCanUpdateItemQuantity(): void
    {
        $this->cart->addItem($this->product, Quantity::fromInt(1));
        $this->cart->updateItemQuantity($this->product->getId(), Quantity::fromInt(3));
        
        $this->assertEquals(3, $this->cart->getItemCount());
        
        $expectedTotal = Money::fromAmount(77.97); // 25.99 * 3
        $this->assertEquals($expectedTotal, $this->cart->getTotalAmount());
    }

    public function testCanRemoveItemFromCart(): void
    {
        $this->cart->addItem($this->product, Quantity::fromInt(2));
        $this->cart->removeItem($this->product->getId());
        
        $this->assertTrue($this->cart->isEmpty());
        $this->assertFalse($this->cart->hasItem($this->product->getId()));
    }

    public function testRemovingNonExistentItemThrowsException(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Item not found in cart');
        
        $this->cart->removeItem(ProductId::generate());
    }

    public function testAddingUnavailableProductThrowsException(): void
    {
        $unavailableProduct = new Product(
            ProductId::generate(),
            'Unavailable Product',
            'Description',
            Money::fromAmount(10.00),
            0 // No stock
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Product is not available in requested quantity');
        
        $this->cart->addItem($unavailableProduct, Quantity::fromInt(1));
    }

    public function testAddingSameItemTwiceIncreasesQuantity(): void
    {
        $this->cart->addItem($this->product, Quantity::fromInt(2));
        $this->cart->addItem($this->product, Quantity::fromInt(1));
        
        $this->assertEquals(3, $this->cart->getItemCount());
    }

    public function testUpdatingQuantityToZeroRemovesItem(): void
    {
        $this->cart->addItem($this->product, Quantity::fromInt(2));
        $this->cart->updateItemQuantity($this->product->getId(), Quantity::fromInt(0));
        
        $this->assertTrue($this->cart->isEmpty());
        $this->assertFalse($this->cart->hasItem($this->product->getId()));
    }
}