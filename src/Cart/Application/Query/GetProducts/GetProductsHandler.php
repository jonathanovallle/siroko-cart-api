<?php

namespace App\Cart\Application\Query\GetProducts;

use App\Cart\Application\DTO\ProductDTO;
use App\Cart\Domain\Repository\ProductRepositoryInterface;

final class GetProductsHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function handle(GetProductsQuery $query): array
    {
        $products = $this->productRepository->findAll();
        
        return array_map(
            fn($product) => ProductDTO::fromProduct($product),
            $products
        );
    }
}