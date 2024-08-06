<?php

declare(strict_types=1);

use App\Messenger\Recount;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'transports' => [
                'recount' => [
                  'dsn' => getenv('MESSENGER_TRANSPORT_DSN')
                ],
            ],

            'routing' => [
                Recount::class => 'recount'
            ],
        ],
    ]);
};
