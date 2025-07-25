<?php

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Cart\Domain\ValueObject\Quantity;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

class QuantityType extends IntegerType
{
    public const NAME = 'quantity';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Quantity
    {
        if ($value === null) {
            return null;
        }

        return Quantity::fromInt($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Quantity) {
            return $value->getValue();
        }

        return $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}