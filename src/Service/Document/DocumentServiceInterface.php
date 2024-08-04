<?php

declare(strict_types=1);

namespace App\Service\Document;

use App\Model\DocumentDto;

interface DocumentServiceInterface
{
    public function saveDocument(DocumentDto $documentDto): void;
}