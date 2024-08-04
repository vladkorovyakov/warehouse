<?php

declare(strict_types=1);

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

final readonly class DocumentDto
{
    public function __construct(
        #[OA\Property(
            property   : 'type',
            description: 'Тип документа',
            enum       : ['RECEIPT', 'EXPENSE', 'INVENTORY'],
            nullable   : false,
        )]
        public string $type,

        #[OA\Property(
            type : 'array',
            items: new OA\Items(
                ref     : new Model(type: ProductsDto::class),
            )
        )]
        /** @var ProductsDto[] $products */
        public array $products,
    ) {
    }
}