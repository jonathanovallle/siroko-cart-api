<?php

namespace App\Tests\Integration\Checkout\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CheckoutControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCanProcessCheckoutSuccessfully(): void
    {
        // Create cart and add items
        $this->client->request('POST', '/api/carts');
        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $cartId = $createResponse['data']['cart_id'];

        $this->client->request('GET', '/api/products');
        $productsResponse = json_decode($this->client->getResponse()->getContent(), true);
        $productId = $productsResponse['data'][0]['id'];

        $this->client->request('POST', "/api/carts/{$cartId}/items", [], [], [], json_encode([
            'product_id' => $productId,
            'quantity' => 1
        ]));

        // Process checkout
        $checkoutData = [
            'cart_id' => $cartId,
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main St',
            'city' => 'Madrid',
            'postal_code' => '28001',
            'country' => 'Spain',
            'payment_data' => [
                'card_number' => '4242424242424242',
                'cvv' => '123',
                'expiry_month' => '12',
                'expiry_year' => '2025'
            ]
        ];

        $this->client->request('POST', '/api/checkout', [], [], [], json_encode($checkoutData));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('order_id', $responseData['data']);
        $this->assertEquals('Order created successfully', $responseData['message']);

        // Verify cart is empty after checkout
        $this->client->request('GET', "/api/carts/{$cartId}");
        $cartResponse = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(0, $cartResponse['data']['item_count']);
    }

    public function testCanGetOrderAfterCheckout(): void
    {
        // Create and process a checkout first
        $this->client->request('POST', '/api/carts');
        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $cartId = $createResponse['data']['cart_id'];

        $this->client->request('GET', '/api/products');
        $productsResponse = json_decode($this->client->getResponse()->getContent(), true);
        $productId = $productsResponse['data'][0]['id'];

        $this->client->request('POST', "/api/carts/{$cartId}/items", [], [], [], json_encode([
            'product_id' => $productId,
            'quantity' => 2
        ]));

        $checkoutData = [
            'cart_id' => $cartId,
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main St',
            'city' => 'Madrid',
            'postal_code' => '28001',
            'country' => 'Spain',
            'payment_data' => [
                'card_number' => '4242424242424242',
                'cvv' => '123'
            ]
        ];

        $this->client->request('POST', '/api/checkout', [], [], [], json_encode($checkoutData));
        $checkoutResponse = json_decode($this->client->getResponse()->getContent(), true);
        $orderId = $checkoutResponse['data']['order_id'];

        // Get order
        $this->client->request('GET', "/api/orders/{$orderId}");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertTrue($responseData['success']);
        $this->assertEquals($orderId, $responseData['data']['id']);
        $this->assertEquals($cartId, $responseData['data']['cart_id']);
        $this->assertEquals('paid', $responseData['data']['status']);
        $this->assertEquals('test@example.com', $responseData['data']['customer_email']);
        $this->assertEquals('John Doe', $responseData['data']['customer_name']);
        $this->assertCount(1, $responseData['data']['items']);
        $this->assertEquals(2, $responseData['data']['items'][0]['quantity']);
    }

    public function testCheckoutWithEmptyCartFails(): void
    {
        $this->client->request('POST', '/api/carts');
        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $cartId = $createResponse['data']['cart_id'];

        $checkoutData = [
            'cart_id' => $cartId,
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main St',
            'city' => 'Madrid',
            'postal_code' => '28001',
            'country' => 'Spain',
            'payment_data' => ['card_number' => '4242424242424242']
        ];

        $this->client->request('POST', '/api/checkout', [], [], [], json_encode($checkoutData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContains('Cart not found or empty', $responseData['message']);
    }

    public function testCheckoutWithMissingFieldsFails(): void
    {
        $incompleteData = [
            'cart_id' => '550e8400-e29b-41d4-a716-446655440000',
            'email' => 'test@example.com'
            // Missing required fields
        ];

        $this->client->request('POST', '/api/checkout', [], [], [], json_encode($incompleteData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertStringContains('is required', $responseData['message']);
    }

    public function testCheckoutWithInvalidEmailFails(): void
    {
        $this->client->request('POST', '/api/carts');
        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $cartId = $createResponse['data']['cart_id'];

        $this->client->request('GET', '/api/products');
        $productsResponse = json_decode($this->client->getResponse()->getContent(), true);
        $productId = $productsResponse['data'][0]['id'];

        $this->client->request('POST', "/api/carts/{$cartId}/items", [], [], [], json_encode([
            'product_id' => $productId,
            'quantity' => 1
        ]));

        $checkoutData = [
            'cart_id' => $cartId,
            'email' => 'invalid-email',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main St',
            'city' => 'Madrid',
            'postal_code' => '28001',
            'country' => 'Spain',
            'payment_data' => ['card_number' => '4242424242424242']
        ];

        $this->client->request('POST', '/api/checkout', [], [], [], json_encode($checkoutData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
    }

    public function testGetNonExistentOrderReturns404(): void
    {
        $fakeOrderId = '550e8400-e29b-41d4-a716-446655440000';
        
        $this->client->request('GET', "/api/orders/{$fakeOrderId}");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Order not found', $responseData['message']);
    }

    public function testMultipleItemsCheckout(): void
    {
        // Create cart
        $this->client->request('POST', '/api/carts');
        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $cartId = $createResponse['data']['cart_id'];

        // Get products
        $this->client->request('GET', '/api/products');
        $productsResponse = json_decode($this->client->getResponse()->getContent(), true);
        $products = $productsResponse['data'];

        // Add multiple items
        $this->client->request('POST', "/api/carts/{$cartId}/items", [], [], [], json_encode([
            'product_id' => $products[0]['id'],
            'quantity' => 2
        ]));

        $this->client->request('POST', "/api/carts/{$cartId}/items", [], [], [], json_encode([
            'product_id' => $products[1]['id'],
            'quantity' => 1
        ]));

        // Process checkout
        $checkoutData = [
            'cart_id' => $cartId,
            'email' => 'multi@example.com',
            'first_name' => 'Multi',
            'last_name' => 'Item',
            'address' => '456 Multi St',
            'city' => 'Barcelona',
            'postal_code' => '08001',
            'country' => 'Spain',
            'payment_data' => ['card_number' => '4242424242424242']
        ];

        $this->client->request('POST', '/api/checkout', [], [], [], json_encode($checkoutData));
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $checkoutResponse = json_decode($this->client->getResponse()->getContent(), true);
        $orderId = $checkoutResponse['data']['order_id'];

        // Verify order has multiple items
        $this->client->request('GET', "/api/orders/{$orderId}");
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertCount(2, $orderResponse['data']['items']);
        $this->assertEquals(3, array_sum(array_column($orderResponse['data']['items'], 'quantity')));
    }
}