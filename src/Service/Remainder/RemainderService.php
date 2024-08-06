<?php

declare(strict_types=1);

namespace App\Service\Remainder;

use App\Entity\ProductRemainder;
use App\Model\DocumentTypes;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

final readonly class RemainderService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function countByDocumentType(string $documentType, int $remainder, int $value): int
    {
        return match ($documentType) {
            DocumentTypes::DOCUMENT_RECEIPT_TYPE => $remainder + $value,
            DocumentTypes::DOCUMENT_EXPENSE_TYPE => $remainder - $value,
            DocumentTypes::DOCUMENT_INVENTORY_TYPE => $value,
            default => throw new RuntimeException('Operation type not found'),
        };
    }

    public function updateProductRemainder(int $productId, int $newRemainder): void
    {
        $productRemainder = $this->entityManager
            ->getRepository(ProductRemainder::class)
            ->findRemainderByProductId($productId);

        if ($productRemainder === null) {
            $productRemainder = new ProductRemainder();
            $productRemainder->setProductId($productId);
        }

        $productRemainder->setValue($newRemainder);
        $this->entityManager->persist($productRemainder);
        $this->entityManager->flush();
    }
}