<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('nelmio_api_doc', [
        'documentation' => [
            'servers' => [
                [
                    'url'         => 'http://warehouse.loc',
                    'description' => 'Warehouse API'
                ],
            ],

            'info'    => [
                'title'       => 'Warehouse',
                'description' => 'My small test work!',
                'version'     => '0.1.0',
            ],
        ],
        'areas'         => [],
    ]);
};
