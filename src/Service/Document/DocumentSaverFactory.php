<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Model\DocumentTypes;
use Exception;

final class DocumentSaverFactory
{
    public function __construct(
        public RecipeDocumentSaver $recipeDocumentSaver,
        public ExpenseDocumentSaver $expenseDocumentSaver,
        public InventoryDocumentSaver $inventoryDocumentSaver,
    ) {
    }

    /**
     * @throws Exception
     */
    public function createDocumentSaver(string $documentType): DocumentSaverInterface
    {
        return match ($documentType) {
            DocumentTypes::DOCUMENT_RECEIPT_TYPE   => $this->recipeDocumentSaver,
            DocumentTypes::DOCUMENT_EXPENSE_TYPE   => $this->expenseDocumentSaver,
            DocumentTypes::DOCUMENT_INVENTORY_TYPE => $this->inventoryDocumentSaver,
            default => throw new Exception('undefined document type'),
        };
    }
}