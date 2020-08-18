<?php

declare(strict_types=1);

use PhpParser\NodeFinder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Yaml\Parser;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Migrify\PhpConfigPrinter\\', __DIR__ . '/../src');

    $services->set(NodeFinder::class);
    $services->set(Parser::class);
};
