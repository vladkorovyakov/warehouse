<?php

declare(strict_types=1);

namespace App\Service\History;

use App\Model\DocumentTypes;
use App\Model\HistoryDto;
use App\Model\HistoryItemDto;
use App\Service\ApiSerializer;
use Exception;
use JsonException;
use RuntimeException;

final readonly class HistoryStreamer
{
    private const REPLACE = '__DOCUMENTS__';

    public function __construct(private iterable $documents, private int $documentsQuantity)
    {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function __invoke(): void
    {
        [$before, $after] = explode('"' . self::REPLACE . '"', $this->buildJSONStructure(), 2);

        echo $before;

        $currentProduct = null;
        $productData = [];
        $serializer = new ApiSerializer();
        $remainder = 0;
        foreach ($this->documents as $count => $document) {
            if ($count === 0) {
                $currentProduct = $document['productId'];
                $productData = new HistoryDto(
                    product: $currentProduct,
                    history: [],
                );
            }

            $isChangeProduct = $currentProduct !== $document['productId'];
            $isLastElement = $count === $this->documentsQuantity - 1;

            if ($isChangeProduct) {
                $remainder = $document['value'];
                echo $serializer->serialize($productData) . ',';
                $currentProduct = $document['productId'];
                $productData = new HistoryDto(
                    product: $currentProduct,
                    history: [
                                 new HistoryItemDto(
                                     type          : $document['type'],
                                     timestamp     : $document['created']->format(DATE_ATOM),
                                     value         : $document['value'],
                                     remainder     : $document['value'],
                                     inventoryError: $document['errorValue'],
                                 ),
                             ],
                );
            } else {
                $remainder = $this->countRemainder($document['type'], $remainder, $document['value']);
                $productData->history [] = new HistoryItemDto(
                    type          : $document['type'],
                    timestamp     : $document['created']->format(DATE_ATOM),
                    value         : $document['value'],
                    remainder     : $remainder,
                    inventoryError: $document['errorValue'],
                );
            }

            if ($count % 500 === 0 && $count !== $this->documentsQuantity) {
                flush();
            }

            if ($isLastElement) {
                echo $serializer->serialize($productData);
            }

        }
        echo $after;
    }

    /**
     * @throws JsonException
     */
    private function buildJSONStructure(): string
    {
        return json_encode(
            [
                'documents' => [self::REPLACE],
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }

    private function countRemainder(string $operationType, int $currentRemainder, int $value): int
    {
        return match ($operationType) {
            DocumentTypes::DOCUMENT_RECEIPT_TYPE   => $currentRemainder + $value,
            DocumentTypes::DOCUMENT_EXPENSE_TYPE   => $currentRemainder - $value,
            DocumentTypes::DOCUMENT_INVENTORY_TYPE => $value,
            default => throw new RuntimeException('Operation type not found'),
        };
    }

}