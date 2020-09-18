<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\HttpKernel;

use Migrify\MigrifyKernel\HttpKernel\AbstractMigrifyKernel;
use Migrify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;

final class PhpConfigPrinterKernel extends AbstractMigrifyKernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new PhpConfigPrinterBundle()];
    }

    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }
}
