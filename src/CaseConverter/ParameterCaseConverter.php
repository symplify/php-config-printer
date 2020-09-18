<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\CaseConverter;

use Migrify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Migrify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Migrify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Migrify\PhpConfigPrinter\Provider\CurrentFilePathProvider;
use Migrify\PhpConfigPrinter\ValueObject\MethodName;
use Migrify\PhpConfigPrinter\ValueObject\VariableName;
use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;

/**
 * Handles this part:
 *
 * parameters: <---
 */
final class ParameterCaseConverter implements CaseConverterInterface
{
    /**
     * @var ArgsNodeFactory
     */
    private $argsNodeFactory;

    /**
     * @var CurrentFilePathProvider
     */
    private $currentFilePathProvider;

    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;

    public function __construct(
        ArgsNodeFactory $argsNodeFactory,
        CurrentFilePathProvider $currentFilePathProvider,
        CommonNodeFactory $commonNodeFactory
    ) {
        $this->argsNodeFactory = $argsNodeFactory;
        $this->currentFilePathProvider = $currentFilePathProvider;
        $this->commonNodeFactory = $commonNodeFactory;
    }

    public function getKey(): string
    {
        return YamlKey::PARAMETERS;
    }

    public function match(string $rootKey, $key, $values): bool
    {
        return $rootKey === YamlKey::PARAMETERS;
    }

    public function convertToMethodCall($key, $values): Expression
    {
        if (is_string($values)) {
            $values = $this->prefixWithDirConstantIfExistingPath($values);
        }

        if (is_array($values)) {
            foreach ($values as $subKey => $subValue) {
                if (! is_string($subValue)) {
                    continue;
                }
                $values[$subKey] = $this->prefixWithDirConstantIfExistingPath($subValue);
            }
        }

        $args = $this->argsNodeFactory->createFromValues([$key, $values]);

        $parametersVariable = new Variable(VariableName::PARAMETERS);
        $methodCall = new MethodCall($parametersVariable, MethodName::SET, $args);

        return new Expression($methodCall);
    }

    /**
     * @return Expr|string
     */
    private function prefixWithDirConstantIfExistingPath(string $value)
    {
        $filePath = $this->currentFilePathProvider->getFilePath();
        if ($filePath === null) {
            return $value;
        }
        $configDirectory = dirname($filePath);

        $possibleConfigPath = $configDirectory . '/' . $value;
        if (is_file($possibleConfigPath) || is_dir($possibleConfigPath)) {
            return $this->commonNodeFactory->createAbsoluteDirExpr($value);
        }

        return $value;
    }
}
