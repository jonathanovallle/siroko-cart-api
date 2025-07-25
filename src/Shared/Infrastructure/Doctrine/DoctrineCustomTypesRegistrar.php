<?php

namespace App\Shared\Infrastructure\Doctrine;

use Doctrine\DBAL\Types\Type;

class DoctrineCustomTypesRegistrar
{
    public static function register(): void
    {
        if (!Type::hasType('cart_id')) {
            Type::addType('cart_id', CartIdType::class);
        }
        
        if (!Type::hasType('product_id')) {
            Type::addType('product_id', ProductIdType::class);
        }
        
        if (!Type::hasType('order_id')) {
            Type::addType('order_id', OrderIdType::class);
        }
        
        if (!Type::hasType('money')) {
            Type::addType('money', MoneyType::class);
        }
        
        if (!Type::hasType('quantity')) {
            Type::addType('quantity', QuantityType::class);
        }
    }
}