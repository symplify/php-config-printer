<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Bundle;

use Migrify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;
use Migrify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;
use Migrify\PhpConfigPrinter\DependencyInjection\Extension\PhpConfigPrinterExtension;
use Migrify\PhpConfigPrinter\Dummy\DummySymfonyVersionFeatureGuard;
use Migrify\PhpConfigPrinter\Dummy\DummyYamlFileContentProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

/**
 * This class is dislocated in non-standard location, so it's not added by symfony/flex
 * to bundles.php and cause app to crash. See https://github.com/symplify/symplify/issues/1952#issuecomment-628765364
 */
final class PhpConfigPrinterBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $this->registerDefaultImplementations($containerBuilder);

        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new PhpConfigPrinterExtension();
    }

    private function registerDefaultImplementations(ContainerBuilder $containerBuilder): void
    {
        // set default implementations, if none provided - for better developer experience out of the box
        if (! $containerBuilder->has(YamlFileContentProviderInterface::class)) {
            $containerBuilder->autowire(DummyYamlFileContentProvider::class)
                ->setPublic(true);
            $containerBuilder->setAlias(YamlFileContentProviderInterface::class, DummyYamlFileContentProvider::class);
        }

        if (! $containerBuilder->has(SymfonyVersionFeatureGuardInterface::class)) {
            $containerBuilder->autowire(DummySymfonyVersionFeatureGuard::class)
                ->setPublic(true);
            $containerBuilder->setAlias(
                SymfonyVersionFeatureGuardInterface::class,
                DummySymfonyVersionFeatureGuard::class
            );
        }
    }
}
