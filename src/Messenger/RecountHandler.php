<?php

declare(strict_types=1);

namespace App\Messenger;

use App\Service\Remainder\RecountRemainderService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RecountHandler
{
    public function __construct(
        private RecountRemainderService $recountRemainderService
    ) {
    }

    public function __invoke(Recount $recount): void
    {
        $this->recountRemainderService->recount($recount);
    }
}