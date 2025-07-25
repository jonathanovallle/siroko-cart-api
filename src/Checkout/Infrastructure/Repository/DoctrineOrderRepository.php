<?php

namespace App\Checkout\Infrastructure\Repository;

use App\Checkout\Domain\Entity\Order;
use App\Checkout\Domain\Repository\OrderRepositoryInterface;
use App\Checkout\Domain\ValueObject\OrderId;
use Doctrine\DBAL\Connection;

class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private Connection $connection) {}

    public function save(Order $order): void
    {
        $customerInfo = $order->getCustomerInfo();
        
        // Insertar o actualizar orden
        $this->connection->executeStatement(
            'INSERT INTO orders (
                id, cart_id, status, total_amount, currency,
                customer_email, customer_first_name, customer_last_name,
                customer_address, customer_city, customer_postal_code, customer_country,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON CONFLICT (id) DO UPDATE SET 
                status = EXCLUDED.status',
            [
                $order->getId()->toString(),
                $order->getCartId()->toString(),
                $order->getStatus()->getValue(),
                $order->getTotalAmount()->getAmount(),
                $order->getTotalAmount()->getCurrency(),
                $customerInfo->getEmail(),
                $customerInfo->getFirstName(),
                $customerInfo->getLastName(),
                $customerInfo->getAddress(),
                $customerInfo->getCity(),
                $customerInfo->getPostalCode(),
                $customerInfo->getCountry(),
                $order->getCreatedAt()->format('Y-m-d H:i:s')
            ]
        );

        // Insertar items de la orden
        foreach ($order->getItems() as $item) {
            $this->connection->executeStatement(
                'INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity)
                 VALUES (?, ?, ?, ?, ?)
                 ON CONFLICT DO NOTHING',
                [
                    $order->getId()->toString(),
                    $item->getProductId()->toString(),
                    $item->getProductName(),
                    $item->getUnitPrice()->getAmount(),
                    $item->getQuantity()->getValue()
                ]
            );
        }
    }

    public function findById(OrderId $id): ?Order
    {
        $result = $this->connection->fetchAssociative(
            'SELECT * FROM orders WHERE id = ?',
            [$id->toString()]
        );
        
        if (!$result) {
            return null;
        }

        // Por simplicidad, retornamos null por ahora
        // En un caso real, reconstruiríamos la entidad completa
        return null;
    }

    public function findByCustomerEmail(string $email): array
    {
        $results = $this->connection->fetchAllAssociative(
            'SELECT * FROM orders WHERE customer_email = ? ORDER BY created_at DESC',
            [$email]
        );
        
        // Por simplicidad, retornamos array vacío por ahora
        return [];
    }
}