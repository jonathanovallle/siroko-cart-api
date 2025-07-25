<?php

namespace App\Checkout\Infrastructure\Repository;

use App\Checkout\Domain\Entity\Order;
use App\Checkout\Domain\Repository\OrderRepositoryInterface;
use App\Checkout\Domain\ValueObject\OrderId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineOrderRepository implements OrderRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Order::class);
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function findById(OrderId $id): ?Order
    {
        return $this->repository->find($id->toString());
    }

    public function findByCustomerEmail(string $email): array
    {
        return $this->repository->createQueryBuilder('o')
            ->where('o.customerInfo.email = :email')
            ->setParameter('email', $email)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}