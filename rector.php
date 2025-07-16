<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;

return RectorConfig::configure()
    ->withPreparedSets(deadCode: true, codeQuality: true, codingStyle: true, typeDeclarations: true, naming: true, privatization: true)
    ->withRootFiles()
    ->withPhpSets()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        '*/Source/*',
        '*/Fixture/*',
        // keep prefix
        StringClassNameToClassConstantRector::class => [
            // keep the container class string, to avoid prefixing
            __DIR__ . '/src/NodeFactory/ContainerConfiguratorReturnClosureFactory.php',
        ],

        // old value is needed
        \Rector\Php81\Rector\MethodCall\MyCLabsMethodCallToEnumConstRector::class,
    ]);
