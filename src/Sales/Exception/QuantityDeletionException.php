<?php

declare(strict_types=1);

namespace Cleancoders\Sales\Exception;

use RuntimeException;

final class QuantityDeletionException extends RuntimeException
{
    public function __construct(int $currentOrderedQuantity, int $quantityToRemove)
    {
        parent::__construct(
            sprintf(
                "You are not allowed to remove more quantity. actual [%d], given [%d].",
                $currentOrderedQuantity,
                $quantityToRemove
            )
        );
    }
}