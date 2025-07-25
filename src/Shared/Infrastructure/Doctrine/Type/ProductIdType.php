<?php

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Cart\Domain\ValueObject\ProductId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class ProductIdType extends GuidType
{
    public const NAME = 'product_id';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ProductId
    {
        if ($value === null) {
            return null;
        }

        return ProductId::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductId) {
            return $value->toString();
        }

        return $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}