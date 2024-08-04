<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import(
        [
            'path'      => '../src/Controller/',
            'namespace' => 'App\Controller',
        ],
        'attribute'
    );
};