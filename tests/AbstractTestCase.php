<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PhpConfigPrinter\Tests\HttpKernel\TestKernel;

abstract class AbstractTestCase extends TestCase
{
    private \Symfony\Component\DependencyInjection\ContainerInterface $container;

    protected function setUp(): void
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        $this->container = $kernel->getContainer();
    }

    /**
     * @template TType as object
     *
     * @param class-string<TType> $type
     * @return TType
     */
    public function getService(string $type): object
    {
        if (! $this->container->has($type)) {
            throw new \RuntimeException(sprintf('Service "%s" not found in container.', $type));
        }

        return $this->container->get($type);
    }
}
