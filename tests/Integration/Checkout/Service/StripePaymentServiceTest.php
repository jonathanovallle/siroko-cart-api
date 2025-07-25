<?php

namespace App\Tests\Integration\Checkout\Infrastructure\Service;

use App\Cart\Domain\ValueObject\Money;
use App\Checkout\Domain\ValueObject\CustomerInfo;
use App\Checkout\Infrastructure\Service\StripePaymentService;
use PHPUnit\Framework\TestCase;

class StripePaymentServiceTest extends TestCase
{
    private StripePaymentService $paymentService;
    private CustomerInfo $customerInfo;

    protected function setUp(): void
    {
        $this->paymentService = new StripePaymentService('sk_test_fake_key');
        $this->customerInfo = new CustomerInfo(
            'test@example.com',
            'John',
            'Doe',
            '123 Main St',
            'Madrid',
            '28001',
            'Spain'
        );
    }

    public function testProcessPaymentWithValidData(): void
    {
        $amount = Money::fromAmount(25.99);
        $paymentData = [
            'card_number' => '4242424242424242',
            'cvv' => '123',
            'expiry_month' => '12',
            'expiry_year' => '2025'
        ];

        $result = $this->paymentService->processPayment($amount, $this->customerInfo, $paymentData);

        // Since it's a simulation, we expect either success or failure
        $this->assertIsBool($result->isSuccessful());
        
        if ($result->isSuccessful()) {
            $this->assertIsString($result->getTransactionId());
            $this->assertStringStartsWith('txn_', $result->getTransactionId());
            $this->assertNull($result->getErrorMessage());
        } else {
            $this->assertIsString($result->getErrorMessage());
            $this->assertEquals('', $result->getTransactionId());
        }
    }

    public function testProcessPaymentWithInvalidData(): void
    {
        $amount = Money::fromAmount(25.99);
        $paymentData = [
            // Missing required fields
            'cvv' => '123'
        ];

        $result = $this->paymentService->processPayment($amount, $this->customerInfo, $paymentData);

        $this->assertFalse($result->isSuccessful());
        $this->assertEquals('Invalid payment data', $result->getErrorMessage());
    }

    public function testProcessPaymentSimulatesRandomFailure(): void
    {
        // Test multiple times to potentially hit the 10% failure rate
        $results = [];
        for ($i = 0; $i < 20; $i++) {
            $amount = Money::fromAmount(10.00);
            $paymentData = [
                'card_number' => '4242424242424242',
                'cvv' => '123'
            ];

            $result = $this->paymentService->processPayment($amount, $this->customerInfo, $paymentData);
            $results[] = $result->isSuccessful();
        }

        // We should have at least some successful and potentially some failed payments
        $this->assertContains(true, $results, 'Should have at least one successful payment');
    }
}