<?php

namespace App\Cart\Infrastructure\Repository;

use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Repository\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Money;
use Doctrine\DBAL\Connection;

class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function __construct(private Connection $connection) {}

    public function findById(ProductId $id): ?Product
    {
        $result = $this->connection->fetchAssociative(
            'SELECT id, name, description, price, stock, active FROM products WHERE id = ? AND active = true',
            [$id->toString()]
        );
        
        if (!$result) {
            return null;
        }

        return new Product(
            ProductId::fromString($result['id']),
            $result['name'],
            $result['description'],
            Money::fromAmount((float) $result['price']),
            (int) $result['stock'],
            (bool) $result['active']
        );
    }

    public function findAll(): array
    {
        $results = $this->connection->fetchAllAssociative(
            'SELECT id, name, description, price, stock, active FROM products WHERE active = true ORDER BY name'
        );
        
        return array_map(function($row) {
            return new Product(
                ProductId::fromString($row['id']),
                $row['name'],
                $row['description'],
                Money::fromAmount((float) $row['price']),
                (int) $row['stock'],
                (bool) $row['active']
            );
        }, $results);
    }

    public function save(Product $product): void
    {
        $this->connection->executeStatement(
            'INSERT INTO products (id, name, description, price, stock, active) 
             VALUES (?, ?, ?, ?, ?, ?) 
             ON CONFLICT (id) DO UPDATE SET 
                name = EXCLUDED.name,
                description = EXCLUDED.description,
                price = EXCLUDED.price,
                stock = EXCLUDED.stock,
                active = EXCLUDED.active',
            [
                $product->getId()->toString(),
                $product->getName(),
                $product->getDescription(),
                $product->getPrice()->getAmount(),
                $product->getStock(),
                $product->isActive()
            ]
        );
    }
}