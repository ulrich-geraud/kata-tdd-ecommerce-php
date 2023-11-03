<?php

declare(strict_types=1);

namespace Cleancoders\Sales\Factory;

use Cleancoders\Sales\Item;

abstract class ItemFactory
{
    /**
     * @param array<string, int|array<string, string|float>> $itemData
     * @return Item
     */
    public static function create(array $itemData): Item
    {
        return new Item(
            product: ProductFactory::create($itemData['product']),
            orderedQuantity: $itemData['ordered_quantity']
        );
    }
}