<?php

declare(strict_types=1);

namespace App\Service\InventoryService;

use App\Entity\Documents;
use App\Entity\ProductRemainder;
use App\Model\InventoryDto;
use App\Repository\DocumentsRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

final readonly class InventoryService implements InventoryServiceInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws RuntimeException
     */
    public function getInventoryDataByDate(DateTimeImmutable $date): array
    {
        $documentsRepository = $this->entityManager->getRepository(Documents::class);
        $inventoryList = $documentsRepository->findInventoryByDate($date);
        if (empty($inventoryList)) {
            throw new RuntimeException('Inventory data not found for date ' . $date->format('Y-m-d'));
        }

        $result = [];
        /**@var Documents $inventory */
        foreach ($inventoryList as $inventory) {
            $product = $inventory->getProductId();
            $documentsReceiptList = $documentsRepository->findLastProductReceipt($product, $date);
            $costPrice = empty($documentsReceiptList)
                ? $this->getLastProductReceiptCost($documentsRepository, $product, $date)
                : $this->calculateSelfPrice($documentsReceiptList);

            $inventoryError = $inventory->getInventoryError()?->getErrorValue() ?? 0;
            $result [] = new InventoryDto(
                product           : $inventory->getProductId(),
                remainder         : $this->getRemainder($inventory->getProductId()),
                remainderCost     : $costPrice * $this->getRemainder($inventory->getProductId()),
                inventoryError    : $inventoryError,
                inventoryErrorCost: $inventoryError * $costPrice,
            );
        }
        return $result;
    }

    private function calculateSelfPrice($documentsReceiptList): float
    {
        $priceSum = 0;
        $remainderSum = 0;

        /**@var Documents $receipt*/
        foreach ($documentsReceiptList as $receipt) {
            $priceSum += $receipt->getPricePerProduct()?->getValue() * $receipt->getValue();
            $remainderSum += $receipt->getValue();
        }

        return $priceSum / $remainderSum;
    }

    private function getLastProductReceiptCost(
        DocumentsRepository $documentsRepository,
        int $productId,
        DateTimeImmutable $date
    ): int {
        $document = $documentsRepository->findLastProductReceiptPrice($productId, $date);
        if ($document === null){
            throw new RuntimeException('Not found receipt for product ' . $productId);
        }
        return $document->getPricePerProduct()?->getValue();
    }

    private function getRemainder(?int $productId): int
    {
        return $this->entityManager->getRepository(ProductRemainder::class)
            ->findRemainderByProductId($productId)
            ?->getValue() ?? 0;
    }
}