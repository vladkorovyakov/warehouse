<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Documents;
use App\Entity\ProductRemainder;
use App\Messenger\Recount;
use App\Model\ProductsDto;
use App\Service\Remainder\RemainderService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractDocumentSaver implements DocumentSaverInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected MessageBusInterface             $messageBus,
        protected RemainderService                $remainderService,
    ) {
    }

    /**
     * @param ProductsDto[] $products
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function createDocumentsForProduct(array $products): void
    {
        foreach ($products as $product) {
            $documentDate = new DateTimeImmutable($product->timestamp);
            $currentRemainder = $this->remainderService
                ->countByDocumentType(
                    $this->getType(),
                    $this->getLastRemainderForProduct($product->productId),
                    $product->quantity
                );
            $isMedianTimestamp = $this->isMedianTimestampForProduct($product->productId, $documentDate);

            $document = new Documents();
            $document->setProductId($product->productId)
                ->setCreated($documentDate)
                ->setType($this->getType())
                ->setValue($product->quantity)
                ->setCurrentRemainder($currentRemainder);
            $document = $this->addAdditionalProperties($document, $product);

            $this->entityManager->persist($document);
            $this->entityManager->flush();

            if ($isMedianTimestamp) {
                $this->sendRecountProductReminder($product->productId, $documentDate, $product->quantity);
            } else {
                $this->remainderService->updateProductRemainder($product->productId, $currentRemainder);
            }
        }
    }

    /**
     * @throws ExceptionInterface
     */
    private function sendRecountProductReminder(int $productId, DateTimeImmutable $date, int $quantity): void
    {
        $this->messageBus->dispatch(new Recount($productId, $quantity,  $date));
    }

    abstract protected function getType(): string;

    abstract protected function addAdditionalProperties(Documents $document, ProductsDto $productsDto): Documents;

    abstract protected function countNewRemainder(int $productId, int $quantity): int;

    /**
     * @throws ExceptionInterface
     */
    private function isMedianTimestampForProduct(int $productId, DateTimeImmutable $documentDate): bool
    {
        $documentRepository = $this->entityManager->getRepository(Documents::class);
        return !$documentRepository->isLatestValueForProduct($productId, $documentDate);
    }

    private function getLastRemainderForProduct(int $productId): int
    {
        return $this->entityManager
            ->getRepository(ProductRemainder::class)
            ->findRemainderByProductId($productId)
            ?->getValue() ?? 0;
    }
}