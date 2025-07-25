<?php

namespace App\Checkout\Infrastructure\Service;

use App\Cart\Domain\ValueObject\Money;
use App\Checkout\Domain\Service\PaymentResult;
use App\Checkout\Domain\Service\PaymentServiceInterface;
use App\Checkout\Domain\ValueObject\CustomerInfo;

class StripePaymentService implements PaymentServiceInterface
{
    public function __construct(private string $stripeSecretKey)
    {
    }

    public function processPayment(Money $amount, CustomerInfo $customer, array $paymentData): PaymentResult
    {
        // Simulamos el procesamiento del pago        
        try {
            if (!isset($paymentData['card_number']) || !isset($paymentData['cvv'])) {
                return PaymentResult::failure('Invalid payment data');
            }

            $transactionId = 'txn_' . uniqid();
            
            if (rand(1, 10) <= 9) {
                return PaymentResult::success($transactionId);
            } else {
                return PaymentResult::failure('Payment declined');
            }
        } catch (\Exception $e) {
            return PaymentResult::failure('Payment processing error: ' . $e->getMessage());
        }
    }
}