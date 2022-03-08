<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Yaml;

use Nette\Utils\Strings;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

/**
 * @api
 * @copy of https://github.com/symplify/symplify/blob/d4beda1b1af847599aa035ead755e03db81c7247/packages/easy-coding-standard/src/Yaml/CheckerServiceParametersShifter.php
 *
 * Before:
 *
 * services:
 *      # fixer
 *      ArrayFixer:
 *          syntax: short
 *      # sniff
 *      ArraySniff:
 *          syntax: short
 *
 * After:
 *
 * services:
 *      # fixer
 *      ArrayFixer:
 *          calls:
 *              - ['configure', [{'syntax' => 'short'}]
 *      # sniff
 *      ArraySniff:
 *          parameters:
 *              $syntax: 'short'
 */
final class CheckerServiceParametersShifter
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @see \Symfony\Component\DependencyInjection\Loader\YamlFileLoader::SERVICE_KEYWORDS
     * @var string[]
     */
    private const SERVICE_KEYWORDS = [
        'alias',
        'parent',
        'class',
        'shared',
        'synthetic',
        'lazy',
        'public',
        'abstract',
        'deprecated',
        'factory',
        'file',
        'arguments',
        'properties',
        'configurator',
        'calls',
        'tags',
        'decorates',
        'decoration_inner_name',
        'decoration_priority',
        'decoration_on_invalid',
        'autowire',
        'autoconfigure',
        'bind',
    ];

    private StringFormatConverter $stringFormatConverter;

    public function __construct()
    {
        $this->stringFormatConverter = new StringFormatConverter();
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    public function process(array $configuration): array
    {
        if (! isset($configuration[self::SERVICES_KEY])) {
            return $configuration;
        }

        if (! is_array($configuration[self::SERVICES_KEY])) {
            return $configuration;
        }

        $configuration[self::SERVICES_KEY] = $this->processServices($configuration[self::SERVICES_KEY]);

        return $configuration;
    }

    /**
     * @param mixed[] $services
     * @return mixed[]
     */
    private function processServices(array $services): array
    {
        foreach ($services as $serviceName => $serviceDefinition) {
            if (! $this->isCheckerClass($serviceName)) {
                continue;
            }

            if ($serviceDefinition === null) {
                continue;
            }

            if ($serviceDefinition === []) {
                continue;
            }

            if (\str_ends_with($serviceName, 'Fixer')) {
                $services = $this->processFixer($services, $serviceName, $serviceDefinition);
            }

            if (\str_ends_with($serviceName, 'Sniff')) {
                $services = $this->processSniff($services, $serviceName, $serviceDefinition);
            }

            // cleanup parameters
            $services = $this->cleanupParameters($services, $serviceDefinition, $serviceName);
        }

        return $services;
    }

    private function isCheckerClass(string $checker): bool
    {
        if (\str_ends_with($checker, 'Fixer')) {
            return true;
        }

        return \str_ends_with($checker, 'Sniff');
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processFixer(array $services, string $checker, array $serviceDefinition): array
    {
        foreach (array_keys($serviceDefinition) as $key) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $serviceDefinition = $this->stringFormatConverter->camelCaseToUnderscoreInArrayKeys($serviceDefinition);

            $services[$checker]['calls'] = [['configure', [$serviceDefinition]]];
        }

        return $services;
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processSniff(array $services, string $checker, array $serviceDefinition): array
    {
        // move parameters to property setters
        foreach ($serviceDefinition as $key => $value) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $key = $this->stringFormatConverter->underscoreAndHyphenToCamelCase($key);

            $services[$checker]['properties'][$key] = $this->escapeValue($value);
        }

        return $services;
    }

    /**
     * @param mixed[] $services
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function cleanupParameters(array $services, array $serviceDefinition, string $serviceName): array
    {
        foreach (array_keys($serviceDefinition) as $key) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            unset($services[$serviceName][$key]);
        }

        return $services;
    }

    private function isReservedKey(string | int $key): bool
    {
        if (! is_string($key)) {
            return false;
        }

        return in_array($key, self::SERVICE_KEYWORDS, true);
    }

    private function escapeValue(mixed $value): mixed
    {
        if (! is_array($value) && ! is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $key => $nestedValue) {
                $value[$key] = $this->escapeValue($nestedValue);
            }

            return $value;
        }

        return Strings::replace($value, '#^@#', '@@');
    }
}
