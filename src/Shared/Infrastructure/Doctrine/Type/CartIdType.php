<?php

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Cart\Domain\ValueObject\CartId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class CartIdType extends GuidType
{
    public const NAME = 'cart_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?CartId
    {
        if ($value === null) {
            return null;
        }

        return CartId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof CartId) {
            return $value->toString();
        }

        return $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}