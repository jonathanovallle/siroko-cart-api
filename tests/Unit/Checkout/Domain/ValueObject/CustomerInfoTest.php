<?php

namespace App\Tests\Unit\Checkout\Domain\ValueObject;

use App\Checkout\Domain\ValueObject\CustomerInfo;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CustomerInfoTest extends TestCase
{
    public function testCanCreateValidCustomerInfo(): void
    {
        $customerInfo = new CustomerInfo(
            'test@example.com',
            'John',
            'Doe', 
            '123 Main St',
            'Madrid',
            '28001',
            'Spain'
        );

        $this->assertEquals('test@example.com', $customerInfo->getEmail());
        $this->assertEquals('John', $customerInfo->getFirstName());
        $this->assertEquals('Doe', $customerInfo->getLastName());
        $this->assertEquals('John Doe', $customerInfo->getFullName());
        $this->assertEquals('123 Main St', $customerInfo->getAddress());
        $this->assertEquals('Madrid', $customerInfo->getCity());
        $this->assertEquals('28001', $customerInfo->getPostalCode());
        $this->assertEquals('Spain', $customerInfo->getCountry());
    }

    public function testInvalidEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        new CustomerInfo(
            'invalid-email',
            'John',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain'
        );
    }

    public function testEmptyFirstNameThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('First name is required');

        new CustomerInfo(
            'test@example.com',
            '',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain'
        );
    }

    public function testEmptyAddressThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Address is required');

        new CustomerInfo(
            'test@example.com',
            'John',
            'Doe',
            '',
            'Madrid',
            '28001',
            'Spain'
        );
    }
}