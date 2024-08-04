<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Documents;
use App\Entity\InventoryErrors;
use App\Entity\ProductRemainder;
use App\Model\DocumentTypes;
use App\Model\ProductsDto;

final class InventoryDocumentSaver extends AbstractDocumentSaver
{
    protected function getType(): string
    {
        return DocumentTypes::DOCUMENT_INVENTORY_TYPE;
    }

    protected function addAdditionalProperties(Documents $document, ProductsDto $productsDto): Documents
    {
        $inventoryErrors = new InventoryErrors();
        $inventoryErrors->setErrorValue($this->countInventoryError($productsDto->productId, $productsDto->quantity))
            ->setDocument($document);
        $document->setInventoryError($inventoryErrors);
        $this->entityManager->persist($inventoryErrors);
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

        $lastRemainder->setValue($quantity);
        $this->entityManager->persist($lastRemainder);
    }

    private function countInventoryError(int $productId, int $newRemainderValue): int
    {
        $lastRemainder = $this->entityManager
            ->getRepository(ProductRemainder::class)
            ->findRemainderByProductId($productId);
        $remainder = $lastRemainder === null ? 0 : $lastRemainder->getValue();
        return $remainder === null ? 0 : $remainder - $newRemainderValue;
    }
}
