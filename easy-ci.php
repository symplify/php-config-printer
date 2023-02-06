<?php

declare(strict_types=1);

use Symplify\EasyCI\Config\EasyCIConfig;
use Symplify\PhpConfigPrinter\CaseConverter\AliasCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ClassServiceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ConfiguredServiceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ExtensionConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ImportCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\NameOnlyServiceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ParameterCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ResourceCaseConverter;
use Symplify\PhpConfigPrinter\CaseConverter\ServicesDefaultsCaseConverter;
use Symplify\PhpConfigPrinter\RoutingCaseConverter\ConditionalEnvRoutingCaseConverter;
use Symplify\PhpConfigPrinter\RoutingCaseConverter\ImportRoutingCaseConverter;
use Symplify\PhpConfigPrinter\RoutingCaseConverter\PathRoutingCaseConverter;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\AbstractServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\ArgumentsServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\AutowiringTypesOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\CallsServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\DecoratesServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\DeprecatedServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\FactoryConfiguratorServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\ParentLazyServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\PropertiesServiceOptionKeyYamlToPhpFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\SharedPublicServiceOptionKeyYamlToPhpFactory;

return static function (EasyCIConfig $easyCIConfig): void {
    $easyCIConfig->typesToSkip([
        AliasCaseConverter::class,
        ClassServiceCaseConverter::class,
        ConfiguredServiceCaseConverter::class,
        ExtensionConverter::class,
        ImportCaseConverter::class,
        NameOnlyServiceCaseConverter::class,
        ParameterCaseConverter::class,
        ResourceCaseConverter::class,
        ServicesDefaultsCaseConverter::class,
        ConditionalEnvRoutingCaseConverter::class,
        ImportRoutingCaseConverter::class,
        PathRoutingCaseConverter::class,
        AbstractServiceOptionKeyYamlToPhpFactory::class,
        ArgumentsServiceOptionKeyYamlToPhpFactory::class,
        AutowiringTypesOptionKeyYamlToPhpFactory::class,
        BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory::class,
        CallsServiceOptionKeyYamlToPhpFactory::class,
        DecoratesServiceOptionKeyYamlToPhpFactory::class,
        DeprecatedServiceOptionKeyYamlToPhpFactory::class,
        FactoryConfiguratorServiceOptionKeyYamlToPhpFactory::class,
        ParentLazyServiceOptionKeyYamlToPhpFactory::class,
        PropertiesServiceOptionKeyYamlToPhpFactory::class,
        SharedPublicServiceOptionKeyYamlToPhpFactory::class,
    ]);
};
