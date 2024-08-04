<?php

declare(strict_types=1);

namespace App\Service\InventoryService;

use DateTimeImmutable;

interface InventoryServiceInterface
{
    public function getInventoryDataByDate(DateTimeImmutable $date): array;
}