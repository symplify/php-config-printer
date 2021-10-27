<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\CaseConverter;

use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\Service\ServicesPhpNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class ResourceCaseConverter implements CaseConverterInterface
{
    public function __construct(
        private ServicesPhpNodeFactory $servicesPhpNodeFactory
    ) {
    }

    public function convertToMethodCall(mixed $key, mixed $values): Expression
    {
        // Due to the yaml behavior that does not allow the declaration of several identical key names.
        if (isset($values['namespace'])) {
            $key = $values['namespace'];
            unset($values['namespace']);
        }

        return $this->servicesPhpNodeFactory->createResource($key, $values);
    }

    public function match(string $rootKey, mixed $key, mixed $values): bool
    {
        return isset($values[YamlKey::RESOURCE]);
    }
}
