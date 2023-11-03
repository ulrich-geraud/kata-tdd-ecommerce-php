<?php

declare(strict_types=1);

namespace Cleancoders\Sales\Factory;

use Cleancoders\Sales\Product;

abstract class ProductFactory
{
    /**
     * @param array<string, string|float> $productData
     * @return Product
     */
    public static function create(array $productData): Product
    {
        return new Product(
            sku: $productData['sku'],
            name: $productData['name'],
            price: $productData['price'],
        );
    }
}