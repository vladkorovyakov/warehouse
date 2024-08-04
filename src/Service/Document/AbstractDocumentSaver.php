<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Entity\Documents;
use App\Model\ProductsDto;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class AbstractDocumentSaver implements DocumentSaverInterface
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    public function createDocumentsForProduct(array $products): void
    {
           foreach ($products as $product) {
               $document = new Documents();
               $document->setProductId($product->productId)
                    ->setCreated(new DateTimeImmutable($product->timestamp))
                    ->setType($this->getType())
                    ->setValue($product->quantity);
               $document = $this->addAdditionalProperties($document, $product);
               $this->countNewRemainder($product->productId, $product->quantity);

               $this->entityManager->persist($document);
               $this->entityManager->flush();
           }
    }

    abstract protected function getType(): string;

    abstract protected function addAdditionalProperties(Documents $document, ProductsDto $productsDto): Documents;

    abstract protected function countNewRemainder(int $productId, int $quantity): void;
}