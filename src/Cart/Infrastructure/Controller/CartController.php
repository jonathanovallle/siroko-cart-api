<?php

namespace App\Cart\Infrastructure\Controller;

use App\Cart\Application\Command\AddItemToCart\AddItemToCartCommand;
use App\Cart\Application\Command\AddItemToCart\AddItemToCartHandler;
use App\Cart\Application\Command\CreateCart\CreateCartCommand;
use App\Cart\Application\Command\CreateCart\CreateCartHandler;
use App\Cart\Application\Command\RemoveItemFromCart\RemoveItemFromCartCommand;
use App\Cart\Application\Command\RemoveItemFromCart\RemoveItemFromCartHandler;
use App\Cart\Application\Command\UpdateCartItem\UpdateCartItemCommand;
use App\Cart\Application\Command\UpdateCartItem\UpdateCartItemHandler;
use App\Cart\Application\Query\GetCart\GetCartQuery;
use App\Cart\Application\Query\GetCart\GetCartHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/carts', name: 'cart_')]
class CartController extends AbstractController
{
    public function __construct(
        private CreateCartHandler $createCartHandler,
        private GetCartHandler $getCartHandler,
        private AddItemToCartHandler $addItemHandler,
        private UpdateCartItemHandler $updateItemHandler,
        private RemoveItemFromCartHandler $removeItemHandler
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        try {
            $cartId = $this->createCartHandler->handle(new CreateCartCommand());
            
            return new JsonResponse([
                'success' => true,
                'data' => ['cart_id' => $cartId]
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{cartId}', name: 'get', methods: ['GET'])]
    public function get(string $cartId): JsonResponse
    {
        try {
            $cart = $this->getCartHandler->handle(new GetCartQuery($cartId));
            
            if (!$cart) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Cart not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'data' => $cart->toArray()
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{cartId}/items', name: 'add_item', methods: ['POST'])]
    public function addItem(string $cartId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['product_id']) || !isset($data['quantity'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'product_id and quantity are required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new AddItemToCartCommand(
                $cartId,
                $data['product_id'],
                (int) $data['quantity']
            );

            $this->addItemHandler->handle($command);

            return new JsonResponse([
                'success' => true,
                'message' => 'Item added to cart successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{cartId}/items/{productId}', name: 'update_item', methods: ['PUT'])]
    public function updateItem(string $cartId, string $productId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['quantity'])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'quantity is required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new UpdateCartItemCommand(
                $cartId,
                $productId,
                (int) $data['quantity']
            );

            $this->updateItemHandler->handle($command);

            return new JsonResponse([
                'success' => true,
                'message' => 'Item updated successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{cartId}/items/{productId}', name: 'remove_item', methods: ['DELETE'])]
    public function removeItem(string $cartId, string $productId): JsonResponse
    {
        try {
            $command = new RemoveItemFromCartCommand($cartId, $productId);
            $this->removeItemHandler->handle($command);

            return new JsonResponse([
                'success' => true,
                'message' => 'Item removed successfully'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}