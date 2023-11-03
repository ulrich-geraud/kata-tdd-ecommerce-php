<?php

declare(strict_types=1);

namespace Cleancoders\Sales;

use Cleancoders\Sales\Exception\NotAllowedQuantityException;
use Cleancoders\Sales\Exception\ProductNotFoundException;
use Cleancoders\Sales\Exception\QuantityDeletionException;

final class Cart
{
    private string $id;

    /**
     * @var array<string, Item>
     */
    private array $items;

    public function __construct()
    {
        $this->id = sha1(uniqid('', true));
        $this->items = [];
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, Item>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        $orderedQuantity = $item->getOrderedQuantity();

        if ($orderedQuantity <= 0) {
            throw new NotAllowedQuantityException($orderedQuantity);
        }

        $productSku = $item->getProduct()->getSku();
        $existingItem = $this->getItem($productSku);

        if ($existingItem !== null) {
            $existingItem->incrementOrderedQuantity($orderedQuantity);
        } else {
            $this->items[$productSku] = $item;
        }

        return $this;
    }

    public function removeItem(string $productSku, ?int $quantityToRemove = null): self
    {
        $item = $this->getItem($productSku);

        if ($item === null) {
            throw new ProductNotFoundException($productSku);
        }

        if ($quantityToRemove === null) {
            unset($this->items[$productSku]);
        } else {
            $orderedQuantity = $item->getOrderedQuantity();
            if ($quantityToRemove > $orderedQuantity) {
                throw new QuantityDeletionException($orderedQuantity, $quantityToRemove);
            }

            $item->decrementOrderedQuantity($quantityToRemove);
            if ($item->getOrderedQuantity() === 0) {
                unset($this->items[$productSku]);
            }
        }

        return $this;
    }

    public function getItem(string $productSku): ?Item
    {
        return $this->items[$productSku] ?? null;
    }

    public function getTotalPrice(): float
    {
        $totalPrice = 0;

        foreach ($this->items as $item) {
            $totalPrice += $item->getProduct()->getPrice() * $item->getOrderedQuantity();
        }

        return $totalPrice;
    }
}