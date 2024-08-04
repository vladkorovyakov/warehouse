<?php

declare(strict_types=1);

namespace App\Model;

use OpenApi\Attributes as OA;

final readonly class ProductsDto
{
    public function __construct(
        #[OA\Property(type: 'integer', example: 1, nullable: false)]
        public int  $productId,
        #[OA\Property(type: 'integer', example: 5, nullable: false)]
        public int  $quantity,
        #[OA\Property(type: 'string', format: 'datetime', example: '2024-01-01 00:00:00', nullable: false)]
        public string  $timestamp,
        #[OA\Property(type: 'integer', example: 100, nullable: true)]
        public ?int $cost,
    ) {
    }
}