<?php

namespace App\Checkout\Domain\Service;

use App\Cart\Domain\ValueObject\Money;
use App\Checkout\Domain\ValueObject\CustomerInfo;

interface PaymentServiceInterface
{
    public function processPayment(Money $amount, CustomerInfo $customer, array $paymentData): PaymentResult;
}
