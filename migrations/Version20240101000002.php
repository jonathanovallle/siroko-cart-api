<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders and order_items tables';
    }

    public function up(Schema $schema): void
    {
        // Orders table
        $this->addSql('CREATE TABLE orders (
            id UUID PRIMARY KEY,
            cart_id UUID NOT NULL,
            status VARCHAR(50) NOT NULL,
            total_amount JSON NOT NULL,
            customer_email VARCHAR(255) NOT NULL,
            customer_first_name VARCHAR(255) NOT NULL,
            customer_last_name VARCHAR(255) NOT NULL,
            customer_address TEXT NOT NULL,
            customer_city VARCHAR(255) NOT NULL,
            customer_postal_code VARCHAR(50) NOT NULL,
            customer_country VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL
        )');

        // Order items table
        $this->addSql('CREATE TABLE order_items (
            id UUID PRIMARY KEY,
            order_id UUID NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
            product_id UUID NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            unit_price JSON NOT NULL,
            quantity INTEGER NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )');

        // Indexes
        $this->addSql('CREATE INDEX idx_orders_status ON orders(status)');
        $this->addSql('CREATE INDEX idx_orders_customer_email ON orders(customer_email)');
        $this->addSql('CREATE INDEX idx_orders_created_at ON orders(created_at)');
        $this->addSql('CREATE INDEX idx_order_items_order_id ON order_items(order_id)');
        $this->addSql('CREATE INDEX idx_order_items_product_id ON order_items(product_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
    }
}