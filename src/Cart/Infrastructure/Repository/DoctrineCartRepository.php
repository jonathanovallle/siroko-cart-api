<?php

namespace App\Cart\Infrastructure\Repository;

use App\Cart\Domain\Entity\Cart;
use App\Cart\Domain\Repository\CartRepositoryInterface;
use App\Cart\Domain\ValueObject\CartId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineCartRepository implements CartRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Cart::class);
    }

    public function save(Cart $cart): void
    {
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function findById(CartId $id): ?Cart
    {
        return $this->repository->find($id->toString());
    }

    public function delete(CartId $id): void
    {
        $cart = $this->findById($id);
        if ($cart) {
            $this->entityManager->remove($cart);
            $this->entityManager->flush();
        }
    }
}