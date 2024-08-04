<?php
declare(strict_types=1);

namespace App\Service\History;


interface HistoryServiceInterface
{
    public function getAllDocuments(): iterable;

    public function getDocumentsQuantity(): int;
}