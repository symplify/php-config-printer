<?php

declare(strict_types=1);

use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\FirstClass;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(FirstClass::class)
        ->call('configure', [['some_key' => 'some_value']]);

    $services->set(SecondClass::class);
};
