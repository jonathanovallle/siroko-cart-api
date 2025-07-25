<?php

namespace App\Cart\Domain\Entity;

use App\Cart\Domain\ValueObject\CartId;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Quantity;
use App\Cart\Domain\ValueObject\Money;
use App\Cart\Domain\Event\CartCreated;
use App\Cart\Domain\Event\ItemAddedToCart;
use App\Cart\Domain\Event\ItemRemovedFromCart;
use App\Cart\Domain\Event\ItemUpdatedInCart;
use DomainException;

class Cart
{
    private array $items = [];
    private array $domainEvents = [];

    public function __construct(private CartId $id, private \DateTime $createdAt)
    {
        $this->recordEvent(new CartCreated($this->id, $this->createdAt));
    }

    public static function create(): self
    {
        return new self(CartId::generate(), new \DateTime());
    }

    public function getId(): CartId
    {
        return $this->id;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function addItem(Product $product, Quantity $quantity): void
    {
        if (!$product->isAvailable($quantity->getValue())) {
            throw new DomainException('Product is not available in requested quantity');
        }

        $productId = $product->getId();
        
        if ($this->hasItem($productId)) {
            $this->items[$productId->toString()]->increaseQuantity($quantity);
            $this->recordEvent(new ItemUpdatedInCart(
                $this->id,
                $productId,
                $this->items[$productId->toString()]->getQuantity()
            ));
        } else {
            $cartItem = new CartItem(
                $productId,
                $product->getName(),
                $product->getPrice(),
                $quantity
            );
            $this->items[$productId->toString()] = $cartItem;
            $this->recordEvent(new ItemAddedToCart($this->id, $productId, $quantity));
        }
    }

    public function updateItemQuantity(ProductId $productId, Quantity $quantity): void
    {
        if (!$this->hasItem($productId)) {
            throw new DomainException('Item not found in cart');
        }

        if ($quantity->isZero()) {
            $this->removeItem($productId);
            return;
        }

        $this->items[$productId->toString()]->updateQuantity($quantity);
        $this->recordEvent(new ItemUpdatedInCart($this->id, $productId, $quantity));
    }

    public function removeItem(ProductId $productId): void
    {
        if (!$this->hasItem($productId)) {
            throw new DomainException('Item not found in cart');
        }

        unset($this->items[$productId->toString()]);
        $this->recordEvent(new ItemRemovedFromCart($this->id, $productId));
    }

    public function hasItem(ProductId $productId): bool
    {
        return isset($this->items[$productId->toString()]);
    }

    public function getItemCount(): int
    {
        return array_sum(
            array_map(
                fn(CartItem $item) => $item->getQuantity()->getValue(),
                $this->items
            )
        );
    }

    public function getTotalAmount(): Money
    {
        if (empty($this->items)) {
            return Money::fromCents(0);
        }

        $total = Money::fromCents(0);
        foreach ($this->items as $item) {
            $total = $total->add($item->getTotalPrice());
        }

        return $total;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function clear(): void
    {
        $this->items = [];
        $this->recordEvent(new CartCleared($this->id));
    }

    public function getDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }

    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}