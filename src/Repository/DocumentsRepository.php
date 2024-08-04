<?php

namespace App\Repository;

use App\Entity\Documents;
use App\Model\DocumentTypes;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Documents>
 */
class DocumentsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Documents::class);
    }

    public function findAllDocumentsIterable(): iterable
    {
        return $this->createQueryBuilder('doc')
            ->select('doc.productId')
            ->addSelect('doc.created')
            ->addSelect('doc.type')
            ->addSelect('doc.value')
            ->addSelect('ie.errorValue')
            ->leftJoin('doc.inventoryError', 'ie')
            ->orderBy('doc.productId', 'ASC')
            ->addOrderBy('doc.created', 'ASC')
            ->getQuery()
            ->toIterable();
    }

    public function getDocumentsQuantity(): int
    {
        return $this->createQueryBuilder('doc')
            ->select('count(doc.productId)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findInventoryByDate(DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('doc')
            ->where('doc.created >= :startDate')
            ->andWhere('doc.created <= :endDate')
            ->andWhere('doc.type = :type')
            ->setParameter('startDate', $date->format('Y-m-d 00:00:00'))
            ->setParameter('endDate', $date->format('Y-m-d 23:59:59'))
            ->setParameter('type', DocumentTypes::DOCUMENT_INVENTORY_TYPE)
            ->getQuery()
            ->getResult();
    }

    public function findLastProductReceipt(int $product, DateTimeImmutable $date): array
    {
        $endDay = $date;
        $startDay = $date->modify('-20 day');

        return $this->createQueryBuilder('doc')
            ->where('doc.productId = :productId')
            ->andWhere('doc.type = :type')
            ->andWhere('doc.created <= :endDate')
            ->andWhere('doc.created >= :startDate')
            ->setParameter('productId', $product)
            ->setParameter('type', DocumentTypes::DOCUMENT_RECEIPT_TYPE)
            ->setParameter('startDate', $startDay->format('Y-m-d'))
            ->setParameter('endDate', $endDay->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    }

    public function findLastProductReceiptPrice(int $productId, DateTimeImmutable $date): ?Documents
    {
        return $this->createQueryBuilder('doc')
            ->where('doc.productId = :productId')
            ->andWhere('doc.type = :type')
            ->andWhere('doc.created <= :endDate')
            ->setParameter('productId', $productId)
            ->setParameter('type', DocumentTypes::DOCUMENT_RECEIPT_TYPE)
            ->setParameter('endDate', $date->format('Y-m-d'))
            ->orderBy('doc.created', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
