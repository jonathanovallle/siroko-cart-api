<?php

namespace App\DataFixtures;

use App\Cart\Domain\Entity\Product;
use App\Cart\Domain\ValueObject\ProductId;
use App\Cart\Domain\ValueObject\Money;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            [
                'name' => 'Gafas de sol Siroko K3s',
                'description' => 'Gafas de sol deportivas con lentes polarizadas, perfectas para ciclismo',
                'price' => 59.95,
                'stock' => 25
            ],
            [
                'name' => 'Casco Siroko SH1',
                'description' => 'Casco aerodinámico para ciclismo de carretera',
                'price' => 149.99,
                'stock' => 15
            ],
            [
                'name' => 'Maillot Siroko Team',
                'description' => 'Maillot técnico de manga corta para ciclismo',
                'price' => 79.90,
                'stock' => 30
            ],
            [
                'name' => 'Culotte Siroko Pro',
                'description' => 'Culotte con badana de alta calidad',
                'price' => 89.95,
                'stock' => 20
            ],
            [
                'name' => 'Bidón Siroko 750ml',
                'description' => 'Bidón deportivo libre de BPA',
                'price' => 12.99,
                'stock' => 50
            ]
        ];

        foreach ($products as $productData) {
            $product = new Product(
                ProductId::generate(),
                $productData['name'],
                $productData['description'],
                Money::fromAmount($productData['price']),
                $productData['stock']
            );

            $manager->persist($product);
        }

        $manager->flush();
    }
}