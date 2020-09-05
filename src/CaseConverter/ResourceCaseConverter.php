<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\CaseConverter;

use Migrify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Migrify\PhpConfigPrinter\NodeFactory\Service\ServicesPhpNodeFactory;
use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use PhpParser\Node\Stmt\Expression;

/**
 * Handles this part:
 *
 * services:
 *     App\\: <--
 *          source: '../src'
 */
final class ResourceCaseConverter implements CaseConverterInterface
{
    /**
     * @var ServicesPhpNodeFactory
     */
    private $servicesPhpNodeFactory;

    public function __construct(ServicesPhpNodeFactory $servicesPhpNodeFactory)
    {
        $this->servicesPhpNodeFactory = $servicesPhpNodeFactory;
    }

    public function convertToMethodCall($key, $values): Expression
    {
        // Due to the yaml behavior that does not allow the declaration of several identical key names.
        if (isset($values['namespace'])) {
            $key = $values['namespace'];
            unset($values['namespace']);
        }

        return $this->servicesPhpNodeFactory->createResource($key, $values);
    }

    public function match(string $rootKey, $key, $values): bool
    {
        return isset($values[YamlKey::RESOURCE]);
    }
}
