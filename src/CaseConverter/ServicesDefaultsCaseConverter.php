<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\CaseConverter;

use Migrify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Migrify\PhpConfigPrinter\NodeFactory\Service\AutoBindNodeFactory;
use Migrify\PhpConfigPrinter\ValueObject\MethodName;
use Migrify\PhpConfigPrinter\ValueObject\VariableName;
use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;

/**
 * Handles this part:
 *
 * services:
 *     _defaults: <---
 */
final class ServicesDefaultsCaseConverter implements CaseConverterInterface
{
    /**
     * @var AutoBindNodeFactory
     */
    private $autoBindNodeFactory;

    public function __construct(AutoBindNodeFactory $autoBindNodeFactory)
    {
        $this->autoBindNodeFactory = $autoBindNodeFactory;
    }

    public function convertToMethodCall($key, $values): Expression
    {
        $methodCall = new MethodCall($this->createServicesVariable(), MethodName::DEFAULTS);
        $methodCall = $this->autoBindNodeFactory->createAutoBindCalls(
            $values,
            $methodCall,
            AutoBindNodeFactory::TYPE_DEFAULTS
        );

        return new Expression($methodCall);
    }

    public function match(string $rootKey, $key, $values): bool
    {
        if ($rootKey !== YamlKey::SERVICES) {
            return false;
        }

        return $key === YamlKey::_DEFAULTS;
    }

    private function createServicesVariable(): Variable
    {
        return new Variable(VariableName::SERVICES);
    }
}
