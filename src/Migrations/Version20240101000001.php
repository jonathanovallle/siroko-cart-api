<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create cart and product tables';
    }

    public function up(Schema $schema): void
    {
        // Products table
        $this->addSql('CREATE TABLE products (
            id UUID PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            price JSON NOT NULL,
            stock INTEGER NOT NULL,
            active BOOLEAN NOT NULL DEFAULT true,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )');

        // Carts table
        $this->addSql('CREATE TABLE carts (
            id UUID PRIMARY KEY,
            created_at TIMESTAMP NOT NULL
        )');

        // Cart items table
        $this->addSql('CREATE TABLE cart_items (
            id UUID PRIMARY KEY,
            cart_id UUID NOT NULL REFERENCES carts(id) ON DELETE CASCADE,
            product_id UUID NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            unit_price JSON NOT NULL,
            quantity INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )');

        // Indexes
        $this->addSql('CREATE INDEX idx_cart_items_cart_id ON cart_items(cart_id)');
        $this->addSql('CREATE INDEX idx_cart_items_product_id ON cart_items(product_id)');
        $this->addSql('CREATE INDEX idx_products_active ON products(active)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('DROP TABLE carts');
        $this->addSql('DROP TABLE products');
    }
}