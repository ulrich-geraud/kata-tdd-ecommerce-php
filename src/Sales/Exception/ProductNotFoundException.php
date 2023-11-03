<?php

declare(strict_types=1);

namespace Cleancoders\Sales\Exception;

use RuntimeException;

final class ProductNotFoundException extends RuntimeException
{
    public function __construct(string $productSku)
    {
        parent::__construct(
            sprintf(
                "Product with sku [%s] does not exist.",
                $productSku
            )
        );
    }
}