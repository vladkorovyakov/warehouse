<?php

declare(strict_types=1);

namespace App\Model;

use OpenApi\Attributes as OA;

final class HistoryItemDto
{
    public function __construct(
        #[OA\Property(
            property   : 'type',
            description: 'Тип документа',
            enum       : ['RECEIPT', 'EXPENSE', 'INVENTORY'],
            example    : 'RECEIPT',
            nullable   : false,
        )]
        public string $type,
        #[OA\Property(
            property   : 'timestamp',
            description: 'Временная метка',
            example    : '2024-00-15T15:52:01+00:00',
            nullable   : false,
        )]
        public string $timestamp,
        #[OA\Property(
            property   : 'value',
            description: 'Количество продукта',
            example    : 3,
            nullable   : false,
        )]
        public int $value,
        #[OA\Property(
            property   : 'remainder',
            description: 'Остаток продукта',
            example    : 3,
            nullable   : false,
        )]
        public int $remainder,
        #[OA\Property(
            property   : 'inventoryError',
            description: 'Ошибка инвентаризации',
            example    : null,
            nullable   : true,
        )]
        public ?int $inventoryError,
    ){
    }
}