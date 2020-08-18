<?php

declare(strict_types=1);

use Migrify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use Migrify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
use Migrify\PhpConfigPrinter\Tests\Configuration\DummySymfonyVersionFeatureGuard;
use Migrify\PhpConfigPrinter\Tests\Provider\DummyYamlFileContentProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->set(DummySymfonyVersionFeatureGuard::class);
    $services->alias(SymfonyVersionFeatureGuardInterface::class, DummySymfonyVersionFeatureGuard::class);

    $services->set(DummyYamlFileContentProvider::class);
    $services->alias(YamlFileContentProviderInterface::class, DummyYamlFileContentProvider::class);
};
