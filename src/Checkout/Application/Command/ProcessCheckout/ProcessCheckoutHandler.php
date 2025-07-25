<?php

namespace App\Checkout\Application\Command\ProcessCheckout;

use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use App\Checkout\Domain\Entity\Order;
use App\Checkout\Domain\Repository\OrderRepositoryInterface;
use App\Checkout\Domain\Service\PaymentServiceInterface;
use App\Checkout\Domain\ValueObject\CustomerInfo;
use DomainException;

final class ProcessCheckoutHandler
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private OrderRepositoryInterface $orderRepository,
        private PaymentServiceInterface $paymentService
    ) {}

    public function handle(ProcessCheckoutCommand $command): string
    {
        $cartId = CartId::fromString($command->getCartId());
        $cart = $this->cartRepository->findById($cartId);

        if (!$cart || $cart->isEmpty()) {
            throw new DomainException('Cart not found or empty');
        }

        $customerInfo = new CustomerInfo(
            $command->getEmail(),
            $command->getFirstName(),
            $command->getLastName(),
            $command->getAddress(),
            $command->getCity(),
            $command->getPostalCode(),
            $command->getCountry()
        );

        $order = Order::fromCart(
            $cartId,
            $customerInfo,
            $cart->getItems(),
            $cart->getTotalAmount()
        );

        $paymentResult = $this->paymentService->processPayment(
            $cart->getTotalAmount(),
            $customerInfo,
            $command->getPaymentData()
        );

        if (!$paymentResult->isSuccessful()) {
            throw new DomainException('Payment failed: ' . $paymentResult->getErrorMessage());
        }

        $order->markAsPaid();

        $this->orderRepository->save($order);

        $cart->clear();
        $this->cartRepository->save($cart);

        return $order->getId()->toString();
    }
}