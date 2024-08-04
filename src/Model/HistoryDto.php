<?php

declare(strict_types=1);

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final class HistoryDto
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
            type : 'array',
            items: new OA\Items(
                ref     : new Model(type: HistoryItemDto::class),
            )
        )]
        /**@var HistoryItemDto[] $history*/
        public array $history,
    ) {
    }
}