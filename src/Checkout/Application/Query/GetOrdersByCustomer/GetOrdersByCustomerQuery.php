<?php

namespace App\Checkout\Application\Query\GetOrdersByCustomer;

final readonly class GetOrdersByCustomerQuery
{
    public function __construct(private string $email) {}

    public function getEmail(): string
    {
        return $this->email;
    }
}