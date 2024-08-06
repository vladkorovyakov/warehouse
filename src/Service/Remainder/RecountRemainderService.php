<?php

declare(strict_types=1);

namespace App\Service\Remainder;


use App\Entity\Documents;
use App\Entity\InventoryErrors;
use App\Messenger\Recount;
use App\Model\DocumentTypes;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

final readonly class RecountRemainderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RemainderService $remainderService,
    ) {
    }

    public function recount(Recount $recount): void
    {
        $documentRepository = $this->entityManager->getRepository(Documents::class);
        $documentsForRecount = $documentRepository
            ->findAllDocumentForProductWithDate($recount->getProductId(), $recount->getStartDate());

        $remainder = 0;
        $totalDocumentsQuantity = count($documentsForRecount);
        foreach ($documentsForRecount as $counter => $document) {

            if ($counter === 0) {
                $remainder = $document->getCurrentRemainder();
            } else {
                $newRemainder = $this->remainderService
                    ->countByDocumentType($document->getType(), $remainder, $document->getValue());

                $document->setCurrentRemainder($newRemainder);
//                $this->entityManager->persist($document);

                echo 'id:'.$document->getId().' oldRemainder:'.  $remainder.' newRemainder:'.$newRemainder.PHP_EOL;

                if ($document->getType() === DocumentTypes::DOCUMENT_INVENTORY_TYPE) {
                    $this->recountInventoryError($document->getId(), $document->getValue(), $remainder);
                }

                $remainder = $newRemainder;

                if ($counter % 500 === 0 || $counter === $totalDocumentsQuantity) {
                    $this->entityManager->flush();
                }
            }

            $this->remainderService->updateProductRemainder($document->getProductId(), $remainder);
        }

    }

    private function recountInventoryError(int $documentId, int $documentValue, int $remainder): void
    {
        $inventoryError = $this->entityManager->getRepository(InventoryErrors::class)->findByDocumentId($documentId);
        if ($inventoryError === null) {
            throw new RuntimeException('Inventory error not found');
        }
        $inventoryError->setErrorValue($documentValue - $remainder);
        $this->entityManager->persist($inventoryError);
        $this->entityManager->flush();
    }
}