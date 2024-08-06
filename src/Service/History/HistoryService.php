<?php

declare(strict_types=1);

namespace App\Service\History;

use App\Entity\Documents;
use Doctrine\ORM\EntityManagerInterface;

final readonly class HistoryService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getAllDocuments(): iterable
    {
        return $this->entityManager->getRepository(Documents::class)->findAllDocumentsIterable();
    }

    public function getDocumentsQuantity(): int
    {
        return $this->entityManager->getRepository(Documents::class)->getDocumentsQuantity();
    }
}
