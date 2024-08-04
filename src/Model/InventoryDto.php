<?php

declare(strict_types=1);

namespace App\Model;

use OpenApi\Attributes as OA;
final class InventoryDto
{
    public function __construct(
        #[OA\Property(
            property   : 'product',
            description: 'id продукта',
            example    : 1,
            nullable   : false
        )]
        public int $product,
        #[OA\Property(
            property   : 'remainder',
            description: 'Остаток',
            example    : 1,
            nullable   : false
        )]
        public int $remainder,
        #[OA\Property(
            property   : 'remainderCost',
            description: 'Остаток в рублях.',
            example    : 100.0,
            nullable   : false
        )]
        public float $remainderCost,
        #[OA\Property(
            property   : 'inventoryError',
            description: 'Ошибка инвентаризации',
            example    : -1,
            nullable   : false
        )]
        public int $inventoryError,
        #[OA\Property(
            property   : 'inventoryErrorCost',
            description: 'Ошибка инвентаризации в рублях',
            example    : -100.0,
            nullable   : false
        )]
        public float $inventoryErrorCost,
    ) {
    }
}