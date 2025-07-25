<?php

namespace App\Checkout\Domain\ValueObject;

use InvalidArgumentException;

final readonly class CustomerInfo
{
    public function __construct(
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $address,
        private string $city,
        private string $postalCode,
        private string $country
    ) {
        $this->validateEmail($email);
        $this->validateRequiredField($firstName, 'First name');
        $this->validateRequiredField($lastName, 'Last name');
        $this->validateRequiredField($address, 'Address');
        $this->validateRequiredField($city, 'City');
        $this->validateRequiredField($postalCode, 'Postal code');
        $this->validateRequiredField($country, 'Country');
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    private function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }

    private function validateRequiredField(string $value, string $fieldName): void
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException("{$fieldName} is required");
        }
    }
}