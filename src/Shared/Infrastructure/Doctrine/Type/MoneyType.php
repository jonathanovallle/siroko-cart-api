<?php

namespace App\Shared\Infrastructure\Doctrine\Type;

use App\Cart\Domain\ValueObject\Money;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class MoneyType extends Type
{
    public const NAME = 'money';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'JSON';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Money
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);
        return Money::fromCents($data['amount'], $data['currency']);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Money) {
            return json_encode([
                'amount' => $value->getCents(),
                'currency' => $value->getCurrency()
            ]);
        }

        return $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}