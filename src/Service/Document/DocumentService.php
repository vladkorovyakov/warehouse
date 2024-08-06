<?php
declare(strict_types=1);

namespace App\Service\Document;

use App\Model\DocumentDto;
use Exception;

final readonly class DocumentService
{
    public function __construct(private DocumentSaverFactory $documentSaverFactory)
    {
    }

    /**
     * @throws Exception
     */
    public function saveDocument(DocumentDto $documentDto): void
    {
        $documentSaver = $this->documentSaverFactory->createDocumentSaver($documentDto->type);
        $documentSaver->createDocumentsForProduct($documentDto->products);
    }
}
