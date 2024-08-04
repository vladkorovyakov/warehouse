<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'default_connection'          => 'default',
            'connections'        => [
                'default'      => [
                    'driver'         => 'pdo_pgsql',
                    'host'           => '%env(resolve:PG_HOST)%',
                    'port'           => '%env(resolve:PG_PORT)%',
                    'dbname'         => '%env(resolve:PG_DATABASE)%',
                    'user'           => '%env(resolve:PG_USERNAME)%',
                    'password'       => '%env(resolve:PG_PASSWORD)%',
                    'server_version' => '%env(resolve:PG_VERSION)%',
                    'charset'        => 'utf8',
                    'mapping_types'  => [
                        'enum_lead_enrichment_status' => 'string',
                        'enum_rg_report_status'       => 'string',
                    ],
                ],
            ],        ],
        'orm' => [
            'auto_generate_proxy_classes' => true,
            'enable_lazy_ghost_objects' => true,
            'report_fields_where_declared' => true,
            'validate_xml_mapping' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'mappings' => [
                'App' => [
                    'type' => 'attribute',
                    'is_bundle' => false,
                    'dir' => '%kernel.project_dir%/src/Entity',
                    'prefix' => 'App\Entity',
                    'alias' => 'App',
                ],
            ],
            'controller_resolver' => [
                'auto_mapping' => false,
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('doctrine', [
            'dbal' => [
                'dbname_suffix' => '_test%env(default::TEST_TOKEN)%',
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('doctrine', [
            'orm' => [
                'auto_generate_proxy_classes' => false,
                'proxy_dir' => '%kernel.build_dir%/doctrine/orm/Proxies',
                'query_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.system_cache_pool',
                ],
                'result_cache_driver' => [
                    'type' => 'pool',
                    'pool' => 'doctrine.result_cache_pool',
                ],
            ],
        ]);
        $containerConfigurator->extension('framework', [
            'cache' => [
                'pools' => [
                    'doctrine.result_cache_pool' => [
                        'adapter' => 'cache.app',
                    ],
                    'doctrine.system_cache_pool' => [
                        'adapter' => 'cache.system',
                    ],
                ],
            ],
        ]);
    }
};
