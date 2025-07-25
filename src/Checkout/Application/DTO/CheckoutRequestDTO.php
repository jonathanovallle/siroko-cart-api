<?php

namespace App\Checkout\Application\DTO;

final readonly class CheckoutRequestDTO
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

    public function toArray(): array
    {
        return [
            'cart_id' => $this->cartId,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'payment_data' => $this->paymentData,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['cart_id'],
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['address'],
            $data['city'],
            $data['postal_code'],
            $data['country'],
            $data['payment_data']
        );
    }
}