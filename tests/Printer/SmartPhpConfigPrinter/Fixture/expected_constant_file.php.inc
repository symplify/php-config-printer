<?php

declare(strict_types=1);

use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithConstants;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(ClassWithConstants::class)
        ->call('configure', [[ClassWithConstants::CONFIG_KEY => 'it is constant', ClassWithConstants::NUMERIC_CONFIG_KEY => 'a lot of numbers']]);
};
