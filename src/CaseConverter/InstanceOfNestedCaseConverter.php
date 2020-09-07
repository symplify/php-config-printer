<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\CaseConverter;

use Migrify\PhpConfigPrinter\Contract\NestedCaseConverterInterface;
use Migrify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Migrify\PhpConfigPrinter\NodeFactory\Service\ServiceOptionNodeFactory;
use Migrify\PhpConfigPrinter\ValueObject\MethodName;
use Migrify\PhpConfigPrinter\ValueObject\VariableName;
use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;

/**
 * Handles this part:
 *
 * services:
 *     _instanceof: <---
 */
final class InstanceOfNestedCaseConverter implements NestedCaseConverterInterface
{
    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;

    /**
     * @var ServiceOptionNodeFactory
     */
    private $serviceOptionNodeFactory;

    public function __construct(
        CommonNodeFactory $commonNodeFactory,
        ServiceOptionNodeFactory $serviceOptionNodeFactory
    ) {
        $this->commonNodeFactory = $commonNodeFactory;
        $this->serviceOptionNodeFactory = $serviceOptionNodeFactory;
    }

    public function convertToMethodCall($key, $values): Expression
    {
        $classConstFetch = $this->commonNodeFactory->createClassReference($key);

        $servicesVariable = new Variable(VariableName::SERVICES);
        $args = [new Arg($classConstFetch)];

        $instanceofMethodCall = new MethodCall($servicesVariable, MethodName::INSTANCEOF, $args);
        $instanceofMethodCall = $this->serviceOptionNodeFactory->convertServiceOptionsToNodes(
            $values,
            $instanceofMethodCall
        );

        $expression = new Expression($instanceofMethodCall);
        $expression->setAttribute('comments', $instanceofMethodCall->getComments());

        return $expression;
    }

    public function match(string $rootKey, $subKey): bool
    {
        if ($rootKey !== YamlKey::SERVICES) {
            return false;
        }

        if (! is_string($subKey)) {
            return false;
        }

        return $subKey === YamlKey::_INSTANCEOF;
    }
}
