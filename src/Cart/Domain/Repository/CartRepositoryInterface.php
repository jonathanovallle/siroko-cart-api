<?php

namespace App\Cart\Domain\Repository;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\ValueObject\CartId;

interface CartRepositoryInterface
{
    public function save(Cart $cart): void;
    public function findById(CartId $id): ?Cart;
    public function delete(CartId $id): void;
}