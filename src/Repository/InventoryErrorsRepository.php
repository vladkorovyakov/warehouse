<?php

namespace App\Repository;

use App\Entity\InventoryErrors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InventoryErrors>
 */
class InventoryErrorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryErrors::class);
    }

    public function findByDocumentId(int $productId): ?InventoryErrors
    {
        return $this->createQueryBuilder('ie')
            ->where('ie.document = :documentId')
            ->setParameter('documentId', $productId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
