<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\CaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final readonly class NameOnlyServiceCaseConverter implements CaseConverterInterface
{
    public function __construct(
        private CommonNodeFactory $commonNodeFactory
    ) {
    }

    public function convertToMethodCallStmt(mixed $key, mixed $values): Stmt
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
