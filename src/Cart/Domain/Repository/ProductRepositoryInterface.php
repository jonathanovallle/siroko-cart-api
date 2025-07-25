<?php

namespace App\Cart\Domain\Repository;

use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\ValueObject\ProductId;

interface ProductRepositoryInterface
{
    public function findById(ProductId $id): ?Product;
    public function findAll(): array;
    public function save(Product $product): void;
}