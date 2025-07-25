<?php

namespace App\Checkout\Infrastructure\Controller;

use App\Checkout\Application\Command\ProcessCheckout\ProcessCheckoutCommand;
use App\Checkout\Application\Command\ProcessCheckout\ProcessCheckoutHandler;
use App\Checkout\Application\Query\GetOrder\GetOrderQuery;
use App\Checkout\Application\Query\GetOrder\GetOrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    public function __construct(
        private ProcessCheckoutHandler $processCheckoutHandler,
        private GetOrderHandler $getOrderHandler
    ) {}

    #[Route('/api/checkout', name: 'checkout', methods: ['POST'])]
    public function checkout(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $requiredFields = [
                'cart_id', 'email', 'first_name', 'last_name',
                'address', 'city', 'postal_code', 'country', 'payment_data'
            ];

            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => "Field {$field} is required"
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $command = new ProcessCheckoutCommand(
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

            $orderId = $this->processCheckoutHandler->handle($command);

            return new JsonResponse([
                'success' => true,
                'data' => ['order_id' => $orderId],
                'message' => 'Order created successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/orders/{orderId}', name: 'get_order', methods: ['GET'])]
    public function getOrder(string $orderId): JsonResponse
    {
        try {
            $order = $this->getOrderHandler->handle(new GetOrderQuery($orderId));
            
            if (!$order) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Order not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $order->toArray()
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}