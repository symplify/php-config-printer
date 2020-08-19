<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

final class PhpConfigPrinterKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
        $loader->load(__DIR__ . '/../config/config.tests.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/php_config_printer';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/php_config_printer_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
