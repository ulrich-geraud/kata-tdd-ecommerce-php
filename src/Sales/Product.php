<?php

declare(strict_types=1);

namespace Cleancoders\Sales;

final class Product
{
    public function __construct(
        private string $sku,
        private string $name,
        private float $price,
    ) {}

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}