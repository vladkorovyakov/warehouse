<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Model\ProductsDto;
use Exception;

interface DocumentSaverInterface
{
    /**
     * @param ProductsDto[] $products
     * @throws Exception
     */
    public function createDocumentsForProduct(array $products): void;
}
