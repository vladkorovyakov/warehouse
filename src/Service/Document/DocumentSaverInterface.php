<?php

declare(strict_types=1);

namespace App\Service\Document;

use Exception;

interface DocumentSaverInterface
{
    /**
     * @throws Exception
     */
    public function createDocumentsForProduct(array $products): void;
}
