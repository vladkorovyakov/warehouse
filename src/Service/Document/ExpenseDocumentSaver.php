<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Documents;
use App\Entity\ProductRemainder;
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

    protected function countNewRemainder(int $productId, int $quantity): void
    {
        $lastRemainder = $this->entityManager
            ->getRepository(ProductRemainder::class)
            ->findRemainderByProductId($productId);

        if ($lastRemainder === null) {
            $lastRemainder = new ProductRemainder();
            $lastRemainder->setProductId($productId);
        }

        $lastRemainder->setValue($lastRemainder->getValue() - $quantity);
        $this->entityManager->persist($lastRemainder);
    }
}
