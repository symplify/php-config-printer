<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\CaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class NameOnlyServiceCaseConverter implements CaseConverterInterface
{
    public function __construct(
        private CommonNodeFactory $commonNodeFactory
    ) {
    }

    public function convertToMethodCall(mixed $key, mixed $values): Expression
    {
        $classConstFetch = $this->commonNodeFactory->createClassReference($key);
        $setMethodCall = new MethodCall(new Variable(VariableName::SERVICES), 'set', [new Arg($classConstFetch)]);

        return new Expression($setMethodCall);
    }

    public function match(string $rootKey, mixed $key, mixed $values): bool
    {
        if ($rootKey !== YamlKey::SERVICES) {
            return false;
        }

        return $values === null || $values === [];
    }
}
