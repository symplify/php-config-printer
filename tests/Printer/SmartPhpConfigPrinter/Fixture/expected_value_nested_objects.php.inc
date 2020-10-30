<?php

declare(strict_types=1);

use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\ClassWithType;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use function Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\custom_inline_objects_function;
use PHPStan\Type\StringType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(SecondClass::class)
        ->call('configure', [['some_key' => custom_inline_objects_function([new ClassWithType(new StringType())])]]);
};
