<?php

declare(strict_types=1);

namespace App\Service\History;

use App\Model\HistoryDto;
use App\Model\HistoryItemDto;
use App\Service\ApiSerializer;
use App\Service\Remainder\RemainderService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JsonException;

final readonly class HistoryStreamer
{
    private const REPLACE = '__DOCUMENTS__';

    public function __construct(
        private iterable $documents,
        private int $documentsQuantity,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function __invoke(): void
    {
        $remainderService = new RemainderService($this->entityManager);

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
                $remainder = $remainderService
                    ->countByDocumentType($document['type'], $remainder, $document['value']);


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
}