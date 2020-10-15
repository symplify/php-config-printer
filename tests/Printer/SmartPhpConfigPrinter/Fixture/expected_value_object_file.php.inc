<?php

declare(strict_types=1);

use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ValueObject\Simple;
use function Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\custom_inline_object_function;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SecondClass::class)
        ->call('configure', [['some_key' => custom_inline_object_function(new Simple('Steve'))]]);
};
