<?php

namespace App\Cart\Infrastructure\Controller;

use App\Cart\Application\DTO\ProductDTO;
use App\Cart\Domain\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products', name: 'product_')]
class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        try {
            $products = $this->productRepository->findAll();
            $productDTOs = array_map(
                fn($product) => ProductDTO::fromProduct($product)->toArray(),
                $products
            );

            return new JsonResponse([
                'success' => true,
                'data' => $productDTOs
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}