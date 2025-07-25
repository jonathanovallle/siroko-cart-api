<?php

namespace App\Checkout\Application\Command\ProcessCheckout;

final readonly class ProcessCheckoutCommand
{
    public function __construct(
        private string $cartId,
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $address,
        private string $city,
        private string $postalCode,
        private string $country,
        private array $paymentData
    ) {}

    public function getCartId(): string
    {
        return $this->cartId;
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

    public function getPaymentData(): array
    {
        return $this->paymentData;
    }
}