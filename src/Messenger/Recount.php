<?php

declare(strict_types=1);

namespace App\Messenger;

use DateTimeImmutable;

final readonly class Recount
{
    public function __construct(
        private int $productId,
        private int $quantity,
        private DateTimeImmutable $startDate,
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }
}