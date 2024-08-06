<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Documents;
use App\Entity\PricePerProduct;
use App\Model\DocumentTypes;
use App\Model\ProductsDto;

final class RecipeDocumentSaver extends AbstractDocumentSaver
{
    protected function getType(): string
    {
        return DocumentTypes::DOCUMENT_RECEIPT_TYPE;
    }

    protected function addAdditionalProperties(Documents $document, ProductsDto $productsDto): Documents
    {
        $pricePerProduct = new PricePerProduct();
        $pricePerProduct->setValue($productsDto->cost);
        $pricePerProduct->setDocument($document);
        $document->setPricePerProduct($pricePerProduct);
        $this->entityManager->persist($pricePerProduct);

        return $document;
    }

    protected function countNewRemainder(int $productId, int $quantity): int
    {
        $lastDocument = $this->entityManager
            ->getRepository(Documents::class)
            ->findLastDocumentForProductAndType($productId, $this->getType());

        return ($lastDocument?->getCurrentRemainder() ?? 0) + $quantity;
    }
}