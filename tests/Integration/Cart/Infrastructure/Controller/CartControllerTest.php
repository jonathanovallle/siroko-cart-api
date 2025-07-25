<?php

namespace App\Tests\Integration\Cart\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CartControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCanCreateCart(): void
    {
        $this->client->request('POST', '/api/carts');

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertTrue($responseData['success']);
        $this->assertArrayHasKey('cart_id', $responseData['data']);
        $this->assertIsString($responseData['data']['cart_id']);
    }

    public function testCanGetCart(): void
    {
        // Create cart first
        $this->client->request('POST', '/api/carts');
        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $cartId = $createResponse['data']['cart_id'];

        // Get cart
        $this->client->request('GET', "/api/carts/{$cartId}");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertTrue($responseData['success']);
        $this->assertEquals($cartId, $responseData['data']['id']);
        $this->assertEquals(0, $responseData['data']['item_count']);
        $this->assertEquals(0, $responseData['data']['total_amount']);
    }

    public function testCanAddItemToCart(): void
    {
        // Create cart
        $this->client->request('POST', '/api/carts');
        $createResponse = json_decode($this->client->getResponse()->getContent(), true);
        $cartId = $createResponse['data']['cart_id'];

        // Get a product (assuming fixtures are loaded)
        $this->client->request('GET', '/api/products');
        $productsResponse = json_decode($this->client->getResponse()->getContent(), true);
        $productId = $productsResponse['data'][0]['id'];

        // Add item to cart
        $this->client->request('POST', "/api/carts/{$cartId}/items", [], [], [], json_encode([
            'product_id' => $productId,
            'quantity' => 2
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($responseData['success']);

        // Verify cart has the item
        $this->client->request('GET', "/api/carts/{$cartId}");
        $cartResponse = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertEquals(2, $cartResponse['data']['item_count']);
        $this->assertCount(1, $cartResponse['data']['items']);
    }

    public function testCanUpdateItemQuantity(): void
    {
        // Create cart and add item
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

        // Update quantity
        $this->client->request('PUT', "/api/carts/{$cartId}/items/{$productId}", [], [], [], json_encode([
            'quantity' => 3
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Verify quantity was updated
        $this->client->request('GET', "/api/carts/{$cartId}");
        $cartResponse = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertEquals(3, $cartResponse['data']['item_count']);
    }

    public function testCanRemoveItemFromCart(): void
    {
        // Create cart and add item
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

        // Remove item
        $this->client->request('DELETE', "/api/carts/{$cartId}/items/{$productId}");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Verify cart is empty
        $this->client->request('GET', "/api/carts/{$cartId}");
        $cartResponse = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertEquals(0, $cartResponse['data']['item_count']);
        $this->assertEmpty($cartResponse['data']['items']);
    }

    public function testGetNonExistentCartReturns404(): void
    {
        $fakeCartId = '550e8400-e29b-41d4-a716-446655440000';
        
        $this->client->request('GET', "/api/carts/{$fakeCartId}");

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Cart not found', $responseData['message']);
    }
}