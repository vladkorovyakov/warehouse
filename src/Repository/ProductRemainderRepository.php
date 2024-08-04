<?php

namespace App\Repository;

use App\Entity\ProductRemainder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductRemainder>
 */
class ProductRemainderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductRemainder::class);
    }

    public function findRemainderByProductId(int $productId): ?ProductRemainder
    {
        return $this->createQueryBuilder('pr')
            ->where('pr.productId = :productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
