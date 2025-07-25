<?php

namespace App\Cart\Infrastructure\Repository;

use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\Repository\ProductRepositoryInterface;
use App\Cart\Domain\ValueObject\ProductId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineProductRepository implements ProductRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Product::class);
    }

    public function findById(ProductId $id): ?Product
    {
        return $this->repository->find($id->toString());
    }

    public function findAll(): array
    {
        return $this->repository->findBy(['active' => true]);
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}