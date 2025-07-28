-- ============================================================================
-- Siroko Cart & Checkout API - Database Setup Script
-- ============================================================================
-- Este script configura la base de datos completa para el sistema de carrito
-- y checkout de Siroko. Basado en los ORM mappings de Doctrine.
--
-- Uso: 
-- docker-compose exec app php bin/console dbal:run-sql "$(cat database/setup.sql)"
-- ============================================================================

-- Crear extensiones necesarias (si no existen)
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================================================
-- TABLAS PRINCIPALES (basadas en ORM mappings)
-- ============================================================================

-- Tabla de productos del catálogo (App\Cart\Domain\Entity\Product)
DROP TABLE IF EXISTS products CASCADE;
CREATE TABLE products (
    id VARCHAR(255) PRIMARY KEY,                    -- ProductId (custom type)
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price VARCHAR(255) NOT NULL,                    -- Money (custom type - stored as string)
    stock INTEGER NOT NULL DEFAULT 0,
    active BOOLEAN NOT NULL DEFAULT true
);

-- Índices para optimizar consultas de productos
CREATE INDEX idx_products_stock ON products(stock);
CREATE INDEX idx_products_active ON products(active);
CREATE INDEX idx_products_price ON products(price);

-- Tabla de carritos (App\Cart\Domain\Entity\Cart)
DROP TABLE IF EXISTS carts CASCADE;
CREATE TABLE carts (
    id VARCHAR(36) PRIMARY KEY,                     -- CartId (custom type)
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Índices para carritos
CREATE INDEX idx_carts_created_at ON carts(created_at);

-- Tabla de items del carrito (App\Cart\Domain\Entity\CartItem)
DROP TABLE IF EXISTS cart_items CASCADE;
CREATE TABLE cart_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(), -- UUID con strategy="UUID"
    cart_id VARCHAR(36) NOT NULL,                   -- CartId (custom type)
    product_id VARCHAR(255) NOT NULL,               -- ProductId (custom type)
    product_name VARCHAR(255) NOT NULL,
    unit_price VARCHAR(255) NOT NULL,               -- Money (custom type)
    quantity INTEGER NOT NULL CHECK (quantity > 0), -- Quantity (custom type)
    
    -- Foreign keys
    CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE
);

-- Índices para cart_items
CREATE INDEX idx_cart_items_cart_id ON cart_items(cart_id);
CREATE INDEX idx_cart_items_product_id ON cart_items(product_id);

-- Tabla de órdenes (App\Checkout\Domain\Entity\Order)
DROP TABLE IF EXISTS orders CASCADE;
CREATE TABLE orders (
    id VARCHAR(36) PRIMARY KEY,                     -- OrderId (custom type)
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    
    -- CustomerInfo (embeddable)
    email VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    
    total_amount VARCHAR(255) NOT NULL,             -- Money (custom type)
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Índices para órdenes
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_email ON orders(email);
CREATE INDEX idx_orders_created_at ON orders(created_at);

-- Tabla de items de la orden (App\Checkout\Domain\Entity\OrderItem)
DROP TABLE IF EXISTS order_items CASCADE;
CREATE TABLE order_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    order_id VARCHAR(36) NOT NULL,
    product_id VARCHAR(255) NOT NULL,               -- ProductId (custom type)
    product_name VARCHAR(255) NOT NULL,
    quantity INTEGER NOT NULL CHECK (quantity > 0), -- Quantity (custom type)
    unit_price VARCHAR(255) NOT NULL,               -- Money (custom type)
    subtotal VARCHAR(255) NOT NULL,                 -- Money (custom type)
    
    -- Foreign keys
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Índices para order_items
CREATE INDEX idx_order_items_order_id ON order_items(order_id);
CREATE INDEX idx_order_items_product_id ON order_items(product_id);

-- ============================================================================
-- DATOS DE EJEMPLO - PRODUCTOS SIROKO
-- ============================================================================

-- Limpiar datos existentes
TRUNCATE TABLE order_items, orders, cart_items, carts, products RESTART IDENTITY CASCADE;

-- Insertar productos de Siroko (usando formato Money como string: "8999:EUR")
INSERT INTO products (id, name, description, price, stock, active) VALUES

-- Gafas de ciclismo Siroko
('siroko-k3s-black', 'Gafas Siroko K3s Black Edition', 'Gafas deportivas con lentes polarizadas y marco ultraligero. Diseño aerodinámico para ciclismo profesional.', '8999:EUR', 50, true),
('siroko-k3s-white', 'Gafas Siroko K3s White Edition', 'Versión blanca de las populares K3s. Perfectas para entrenamientos intensos bajo el sol.', '8999:EUR', 45, true),
('siroko-tech-polarized', 'Gafas Siroko Tech Polarizadas', 'Tecnología avanzada con lentes polarizadas. Máximo rendimiento para competición.', '12999:EUR', 30, true),
('siroko-m2-blue', 'Gafas Siroko M2 Blue Gradient', 'Diseño elegante con gradiente azul. Perfectas para ciclismo urbano y de carretera.', '7999:EUR', 75, true),
('siroko-m2-red', 'Gafas Siroko M2 Red Gradient', 'Versión roja deportiva. Combina estilo y funcionalidad para ciclistas exigentes.', '7999:EUR', 60, true),

-- Equipamiento de ciclismo
('jersey-pro-team-2024', 'Maillot Pro Team Siroko 2024', 'Maillot oficial del equipo profesional. Tejido técnico transpirable con corte aerodinámico.', '6999:EUR', 25, true),
('jersey-aero-black', 'Maillot Aero Negro Siroko', 'Diseño aerodinámico premium. Ideal para contrarreloj y competiciones de alta velocidad.', '7999:EUR', 20, true),
('culotte-pro-bib', 'Culotte Pro Bib Siroko', 'Culotte con tirantes profesional. Badana italiana y tejido compresivo de alta gama.', '8999:EUR', 30, true),

-- Accesorios
('bottle-siroko-blue', 'Bidón Siroko Azul 750ml', 'Bidón deportivo con válvula push-pull. Libre de BPA y diseño ergonómico.', '1499:EUR', 100, true),
('bottle-siroko-white', 'Bidón Siroko Blanco 750ml', 'Versión blanca del bidón clásico. Perfecto para entrenamientos largos.', '1499:EUR', 95, true),
('cycling-socks-pro', 'Calcetines Ciclismo Pro Siroko', 'Calcetines técnicos con compresión graduada. Fibras antibacterianas y costuras planas.', '1899:EUR', 200, true),
('cycling-cap-team', 'Gorra Ciclismo Team Siroko', 'Gorra oficial del equipo. Algodón técnico y visera curvada para máxima protección.', '2499:EUR', 80, true),

-- Equipamiento de seguridad
('helmet-aero-black', 'Casco Aero Black Edition', 'Casco aerodinámico de competición. Certificación CE y sistema de ajuste micrométrico.', '15999:EUR', 15, true),
('helmet-road-white', 'Casco Road Blanco Siroko', 'Casco de carretera con ventilación optimizada. Ligero y cómodo para largas distancias.', '12999:EUR', 25, true),
('gloves-summer-white', 'Guantes Verano Blancos Pro', 'Guantes cortos con gel en palma. Tejido transpirable y cierre de velcro ajustable.', '2999:EUR', 80, true),
('gloves-winter-black', 'Guantes Invierno Negros Pro', 'Guantes largos impermeables. Ideal para entrenamientos en condiciones adversas.', '3999:EUR', 40, true),

-- Productos premium
('kit-complete-pro', 'Kit Completo Pro Siroko', 'Kit completo: gafas K3s + maillot + culotte + accesorios. Todo lo necesario para el ciclista profesional.', '24999:EUR', 10, true),
('sunglasses-limited-gold', 'Gafas Edición Limitada Oro', 'Edición limitada con acabados dorados. Numeradas y con certificado de autenticidad.', '19999:EUR', 5, true);

-- ============================================================================
-- CARRITOS Y ÓRDENES DE EJEMPLO
-- ============================================================================

-- Insertar algunos carritos de ejemplo (usando formato correcto para CartId)
INSERT INTO carts (id, created_at) VALUES
('cart-demo-001', CURRENT_TIMESTAMP),
('cart-demo-002', CURRENT_TIMESTAMP);

-- Items en los carritos de ejemplo (usando formato Money como string)
INSERT INTO cart_items (cart_id, product_id, product_name, unit_price, quantity) VALUES
('cart-demo-001', 'siroko-k3s-black', 'Gafas Siroko K3s Black Edition', '8999:EUR', 1),
('cart-demo-001', 'jersey-pro-team-2024', 'Maillot Pro Team Siroko 2024', '6999:EUR', 2),
('cart-demo-002', 'siroko-tech-polarized', 'Gafas Siroko Tech Polarizadas', '12999:EUR', 1);

-- Orden de ejemplo completada (usando CustomerInfo embeddable)
INSERT INTO orders (id, status, email, first_name, last_name, address, city, postal_code, country, total_amount, created_at, updated_at) VALUES
('order-demo-001', 'completed', 'juan.perez@example.com', 'Juan', 'Pérez', 'Calle Mayor 123', 'Madrid', '28001', 'España', '15998:EUR', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- Items de la orden de ejemplo
INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal) VALUES
('order-demo-001', 'siroko-k3s-black', 'Gafas Siroko K3s Black Edition', 1, '8999:EUR', '8999:EUR'),
('order-demo-001', 'jersey-pro-team-2024', 'Maillot Pro Team Siroko 2024', 1, '6999:EUR', '6999:EUR');

-- ============================================================================
-- VISTAS Y FUNCIONES ÚTILES
-- ============================================================================

-- Vista para productos con stock bajo
CREATE OR REPLACE VIEW low_stock_products AS
SELECT 
    id,
    name,
    stock,
    SPLIT_PART(price, ':', 1)::INTEGER/100.0 AS price_eur,
    SPLIT_PART(price, ':', 2) AS currency,
    active
FROM products 
WHERE stock < 20 AND active = true
ORDER BY stock ASC;

-- Vista para resumen de órdenes
CREATE OR REPLACE VIEW order_summary AS
SELECT 
    o.id,
    CONCAT(o.first_name, ' ', o.last_name) AS customer_name,
    o.email,
    o.status,
    SPLIT_PART(o.total_amount, ':', 1)::INTEGER/100.0 AS total_eur,
    COUNT(oi.id) AS total_items,
    o.created_at
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, o.first_name, o.last_name, o.email, o.status, o.total_amount, o.created_at
ORDER BY o.created_at DESC;

-- Vista para carritos activos
CREATE OR REPLACE VIEW active_carts AS
SELECT 
    c.id,
    COUNT(ci.id) AS item_count,
    SUM(SPLIT_PART(ci.unit_price, ':', 1)::INTEGER * ci.quantity)/100.0 AS total_eur,
    c.created_at
FROM carts c
LEFT JOIN cart_items ci ON c.id = ci.cart_id
GROUP BY c.id, c.created_at
HAVING COUNT(ci.id) > 0
ORDER BY c.created_at DESC;

-- ============================================================================
-- CONSULTAS DE VERIFICACIÓN
-- ============================================================================

-- Mostrar resumen de datos insertados
SELECT 
    'Productos' as tipo, 
    COUNT(*) as cantidad,
    COUNT(CASE WHEN active THEN 1 END) as activos
FROM products
UNION ALL
SELECT 
    'Carritos' as tipo, 
    COUNT(*) as cantidad,
    COUNT(CASE WHEN id IS NOT NULL THEN 1 END) as activos
FROM carts
UNION ALL
SELECT 
    'Órdenes' as tipo, 
    COUNT(*) as cantidad,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completadas
FROM orders;

-- ============================================================================
-- COMENTARIOS FINALES
-- ============================================================================

-- Database setup completed successfully!
-- 
-- ✅ Estructura basada en ORM mappings reales
-- ✅ Custom types: cart_id, product_id, money, quantity
-- ✅ Embeddable CustomerInfo en orders
-- ✅ UUIDs para cart_items y order_items
-- ✅ 18 productos de Siroko con descripciones reales
-- ✅ Money format: "amount:currency" (ej: "8999:EUR")
--
-- Para verificar la instalación:
-- SELECT * FROM products WHERE active = true LIMIT 5;
-- SELECT * FROM low_stock_products;
-- SELECT * FROM order_summary;
-- SELECT * FROM active_carts;