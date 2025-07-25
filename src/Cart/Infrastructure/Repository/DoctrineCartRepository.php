<?php

namespace App\Cart\Infrastructure\Repository;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use Doctrine\DBAL\Connection;

class DoctrineCartRepository implements CartRepositoryInterface
{
    public function __construct(private Connection $connection) {}

    public function save(Cart $cart): void
    {
        // Por ahora, implementación simple con DBAL
        $this->connection->executeStatement(
            'INSERT INTO carts (id, created_at) VALUES (?, ?) ON CONFLICT (id) DO NOTHING',
            [$cart->getId()->toString(), $cart->getCreatedAt()->format('Y-m-d H:i:s')]
        );
    }

    public function findById(CartId $id): ?Cart
    {
        // Implementación simplificada - retorna un cart básico
        $result = $this->connection->fetchAssociative(
            'SELECT id, created_at FROM carts WHERE id = ?',
            [$id->toString()]
        );
        
        if (!$result) {
            return null;
        }

        // Crear cart básico para que funcione
        return new Cart($id, new \DateTime($result['created_at']));
    }

    public function delete(CartId $id): void
    {
        $this->connection->executeStatement(
            'DELETE FROM carts WHERE id = ?',
            [$id->toString()]
        );
    }
}