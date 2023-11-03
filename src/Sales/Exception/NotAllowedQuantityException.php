<?php

declare(strict_types=1);

namespace Cleancoders\Sales\Exception;

use RuntimeException;

final class NotAllowedQuantityException extends RuntimeException
{
    public function __construct(int $value)
    {
        parent::__construct(
            sprintf(
                "You are not allowed to add item with quantity less or equal to zero. [%d] given.",
                $value
            )
        );
    }
}