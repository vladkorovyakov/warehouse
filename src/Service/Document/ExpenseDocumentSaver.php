<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Documents;
use App\Model\DocumentTypes;
use App\Model\ProductsDto;

final class ExpenseDocumentSaver extends AbstractDocumentSaver
{
    protected function getType(): string
    {
        return DocumentTypes::DOCUMENT_EXPENSE_TYPE;
    }

    protected function addAdditionalProperties(Documents $document, ProductsDto $productsDto): Documents
    {
        return $document;
    }

    protected function countNewRemainder(int $productId, int $quantity): int
    {
        $lastDocument = $this->entityManager
            ->getRepository(Documents::class)
            ->findLastDocumentForProduct($productId, $this->getType());

        return ($lastDocument?->getCurrentRemainder() ?? 0) - $quantity;
    }
}
