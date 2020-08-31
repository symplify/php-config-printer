<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Printer;

use Migrify\PhpConfigPrinter\NodeFactory\ContainerConfiguratorReturnClosureFactory;

/**
 * @see \Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\SmartPhpConfigPrinterTest
 */
final class SmartPhpConfigPrinter
{
    /**
     * @var ContainerConfiguratorReturnClosureFactory
     */
    private $configuratorReturnClosureFactory;

    /**
     * @var PhpParserPhpConfigPrinter
     */
    private $phpParserPhpConfigPrinter;

    public function __construct(
        ContainerConfiguratorReturnClosureFactory $configuratorReturnClosureFactory,
        PhpParserPhpConfigPrinter $phpParserPhpConfigPrinter
    ) {
        $this->configuratorReturnClosureFactory = $configuratorReturnClosureFactory;
        $this->phpParserPhpConfigPrinter = $phpParserPhpConfigPrinter;
    }

    /**
     * @param array<string, mixed[]|null> $configuredServices
     */
    public function printConfiguredServices(array $configuredServices): string
    {
        $servicesWithConfigureCalls = [];
        foreach ($configuredServices as $service => $configuration) {
            $servicesWithConfigureCalls[$service] = $this->createServiceConfiguration($configuration);
        }

        $return = $this->configuratorReturnClosureFactory->createFromYamlArray(
            ['services' => $servicesWithConfigureCalls]
        );

        return $this->phpParserPhpConfigPrinter->prettyPrintFile([$return]);
    }

    /**
     * @param mixed[]|null $configuration
     */
    private function createServiceConfiguration($configuration): ?array
    {
        if ($configuration === null || $configuration === []) {
            return null;
        }

        return [
            'calls' => [['configure', [$configuration]]],
        ];
    }
}
