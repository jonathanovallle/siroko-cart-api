<?php

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Checkout\Domain\ValueObject\OrderId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class OrderIdType extends GuidType
{
    public const NAME = 'order_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?OrderId
    {
        if ($value === null) {
            return null;
        }

        return OrderId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof OrderId) {
            return $value->toString();
        }

        return $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}