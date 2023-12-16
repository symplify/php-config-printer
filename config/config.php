<?php

declare(strict_types=1);

use PhpParser\BuilderFactory;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitor\ParentConnectingVisitor;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Yaml\Parser;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\PhpConfigPrinter\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/ValueObject']);

    $services->load(
        'Symplify\\PhpConfigPrinter\\CaseConverter\\',
        __DIR__ . '/../src/CaseConverter'
    )->tag(CaseConverterInterface::class);

    $services->set(ContainerConfiguratorReturnClosureFactory::class)
        ->arg('$caseConverters', tagged_iterator(CaseConverterInterface::class));

    $services->set(NodeFinder::class);
    $services->set(Parser::class);
    $services->set(BuilderFactory::class);
    $services->set(ParentConnectingVisitor::class);
};
