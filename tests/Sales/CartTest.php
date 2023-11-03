<?php

declare(strict_types=1);

namespace Cleancoders\Tests\Sales;

use Cleancoders\Sales\Cart;
use Cleancoders\Sales\Exception\NotAllowedQuantityException;
use Cleancoders\Sales\Exception\ProductNotFoundException;
use Cleancoders\Sales\Exception\QuantityDeletionException;
use Cleancoders\Sales\Factory\ItemFactory;
use PHPUnit\Framework\TestCase;

final class CartTest extends TestCase
{
    private Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cart = new Cart();
    }

    public function test_can_create_a_new_cart(): void
    {
        $this->assertInstanceOf(Cart::class, $this->cart);
    }

    public function test_cart_must_be_unique(): void
    {
        $secondCart = new Cart();
        $this->assertNotEquals($this->cart->getId(), $secondCart->getId());
    }

    public function test_a_new_cart_must_be_empty_by_default(): void
    {
        $this->assertCount(0, $this->cart->getItems());
    }

    public function test_add_one_product_into_cart(): void
    {
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 1
        ]));

        $this->assertCount(1, $this->cart->getItems());

        $item = $this->cart->getItem('ABCDEF');

        $this->assertEquals('ABCDEF', $item->getProduct()->getSku());
        $this->assertEquals(1, $item->getOrderedQuantity());
    }

    public function test_add_two_different_products_into_cart(): void
    {
        $items = [
            [
                'product' => [
                    'sku' => 'ABCDEF',
                    'name' => 'Pent',
                    'price' => 10.99,
                ],
                'ordered_quantity' => 1
            ],
            [
                'product' => [
                    'sku' => 'HDJSUT',
                    'name' => 'Shirt',
                    'price' => 20.99,
                ],
                'ordered_quantity' => 3
            ]
        ];

        foreach ($items as $itemData) {
            $this->cart->addItem(ItemFactory::create($itemData));
        }

        $this->assertCount(2, $this->cart->getItems());

        $item = $this->cart->getItem('HDJSUT');

        $this->assertEquals('HDJSUT', $item->getProduct()->getSku());
        $this->assertEquals(3, $item->getOrderedQuantity());
    }

    public function test_add_one_product_many_time_into_cart(): void
    {
        $items = [
            [
                'product' => [
                    'sku' => 'ABCDEF',
                    'name' => 'Pent',
                    'price' => 10.99,
                ],
                'ordered_quantity' => 1
            ],
            [
                'product' => [
                    'sku' => 'ABCDEF',
                    'name' => 'Shirt',
                    'price' => 20.99,
                ],
                'ordered_quantity' => 3
            ]
        ];

        foreach ($items as $itemData) {
            $this->cart->addItem(ItemFactory::create($itemData));
        }

        $this->assertCount(1, $this->cart->getItems());

        $item = $this->cart->getItem('ABCDEF');

        $this->assertEquals('ABCDEF', $item->getProduct()->getSku());
        $this->assertEquals(4, $item->getOrderedQuantity());
    }

    public function test_add_one_product_into_cart_with_zero_quantity(): void
    {
        $this->expectException(NotAllowedQuantityException::class);
        $this->expectExceptionMessage('You are not allowed to add item with quantity less or equal to zero. [0] given.');
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 0
        ]));
    }

    public function test_add_one_product_into_cart_with_negative_quantity(): void
    {
        $this->expectException(NotAllowedQuantityException::class);
        $this->expectExceptionMessage('You are not allowed to add item with quantity less or equal to zero. [-1] given.');
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => -1
        ]));
    }

    public function test_add_one_product_into_cart_and_remove_this_one(): void
    {
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 1
        ]));

        $this->assertCount(1, $this->cart->getItems());

        $this->cart->removeItem('ABCDEF');

        $this->assertNull($this->cart->getItem('ABCDEF'));
        $this->assertCount(0, $this->cart->getItems());
    }

    public function test_add_one_product_into_cart_and_remove_not_existing(): void
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('Product with sku [HDTEGSH] does not exist.');
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 1
        ]));

        $this->assertCount(1, $this->cart->getItems());
        $this->cart->removeItem('HDTEGSH');
    }

    public function test_add_one_product_many_times_into_cart_and_remove_some_quantity(): void
    {
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 10
        ]));

        $this->cart->removeItem('ABCDEF', 3);

        $this->assertCount(1, $this->cart->getItems());

        $item = $this->cart->getItem('ABCDEF');
        $this->assertEquals(7, $item->getOrderedQuantity());
    }

    public function test_add_one_product_many_times_into_cart_and_remove_all_quantity(): void
    {
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 10
        ]));

        $this->cart->removeItem('ABCDEF', 10);
        $this->assertCount(0, $this->cart->getItems());
    }

    public function test_add_one_product_many_times_into_cart_and_remove_more_quantity(): void
    {
        $this->expectException(QuantityDeletionException::class);
        $this->expectExceptionMessage('You are not allowed to remove more quantity. actual [3], given [4].');
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 3
        ]));

        $this->cart->removeItem('ABCDEF', 4);
    }

    public function test_get_empty_cart_total_price(): void
    {
        $this->assertEquals(0, $this->cart->getTotalPrice());
    }

    public function test_add_one_product_and_get_cart_total_price(): void
    {
        $this->cart->addItem(ItemFactory::create([
            'product' => [
                'sku' => 'ABCDEF',
                'name' => 'Pent',
                'price' => 10.99,
            ],
            'ordered_quantity' => 3
        ]));

        $this->assertEquals(10.99 * 3, $this->cart->getTotalPrice());
    }

    public function test_add_two_products_and_get_cart_total_price(): void
    {
        $items = [
            [
                'product' => [
                    'sku' => 'ABCDEF',
                    'name' => 'Pent',
                    'price' => 100.99,
                ],
                'ordered_quantity' => 3
            ],
            [
                'product' => [
                    'sku' => 'HDSHDKE',
                    'name' => 'Shirt',
                    'price' => 200.99,
                ],
                'ordered_quantity' => 5
            ]
        ];

        foreach ($items as $itemData) {
            $this->cart->addItem(ItemFactory::create($itemData));
        }

        $this->assertEquals((100.99 * 3) + (200.99 * 5), $this->cart->getTotalPrice());
    }

    public function test_add_one_product_many_times_and_get_cart_total_price(): void
    {
        $items = [
            [
                'product' => [
                    'sku' => 'ABCDEF',
                    'name' => 'Pent',
                    'price' => 100.99,
                ],
                'ordered_quantity' => 3
            ],
            [
                'product' => [
                    'sku' => 'ABCDEF',
                    'name' => 'Pent',
                    'price' => 100.99,
                ],
                'ordered_quantity' => 5
            ]
        ];

        foreach ($items as $itemData) {
            $this->cart->addItem(ItemFactory::create($itemData));
        }

        $this->assertEquals((100.99 * 8), $this->cart->getTotalPrice());
    }
}