<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\NodeFactory;

use Migrify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Migrify\PhpConfigPrinter\Contract\NestedCaseConverterInterface;
use Migrify\PhpConfigPrinter\PhpParser\NodeFactory\ConfiguratorClosureNodeFactory;
use Migrify\PhpConfigPrinter\ValueObject\MethodName;
use Migrify\PhpConfigPrinter\ValueObject\VariableName;
use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;

final class ContainerConfiguratorReturnClosureFactory
{
    /**
     * @var ConfiguratorClosureNodeFactory
     */
    private $configuratorClosureNodeFactory;

    /**
     * @var CaseConverterInterface[]
     */
    private $caseConverters = [];

    /**
     * @var NestedCaseConverterInterface[]
     */
    private $nestedCaseConverters = [];

    /**
     * @param CaseConverterInterface[] $caseConverters
     * @param NestedCaseConverterInterface[] $nestedCaseConverters
     */
    public function __construct(
        ConfiguratorClosureNodeFactory $configuratorClosureNodeFactory,
        array $caseConverters,
        array $nestedCaseConverters
    ) {
        $this->configuratorClosureNodeFactory = $configuratorClosureNodeFactory;
        $this->caseConverters = $caseConverters;
        $this->nestedCaseConverters = $nestedCaseConverters;
    }

    public function createFromYamlArray(array $arrayData): Return_
    {
        $stmts = $this->createClosureStmts($arrayData);
        $closure = $this->configuratorClosureNodeFactory->createContainerClosureFromStmts($stmts);

        return new Return_($closure);
    }

    /**
     * @return Node[]
     */
    private function createClosureStmts(array $yamlData): array
    {
        $yamlData = array_filter($yamlData);
        return $this->createNodesFromCaseConverters($yamlData);
    }

    /**
     * @param mixed[] $yamlData
     * @return Node[]
     */
    private function createNodesFromCaseConverters(array $yamlData): array
    {
        $nodes = [];

        foreach ($yamlData as $key => $values) {
            $nodes = $this->createInitializeNode($key, $nodes);

            foreach ($values as $nestedKey => $nestedValues) {
                $expression = null;

                $nestedNodes = [];

                if (is_array($nestedValues)) {
                    foreach ($nestedValues as $subNestedKey => $subNestedValue) {
                        foreach ($this->nestedCaseConverters as $nestedCaseConverter) {
                            if (! $nestedCaseConverter->match($key, $nestedKey)) {
                                continue;
                            }

                            $expression = $nestedCaseConverter->convertToMethodCall($subNestedKey, $subNestedValue);
                            $nestedNodes[] = $expression;
                        }
                    }
                }

                if ($nestedNodes !== []) {
                    $nodes = array_merge($nodes, $nestedNodes);
                    continue;
                }

                foreach ($this->caseConverters as $caseConverter) {
                    if (! $caseConverter->match($key, $nestedKey, $nestedValues)) {
                        continue;
                    }

                    /** @var string $nestedKey */
                    $expression = $caseConverter->convertToMethodCall($nestedKey, $nestedValues);
                    break;
                }

                if ($expression === null) {
                    continue;
                }

                $nodes[] = $expression;
            }
        }

        return $nodes;
    }

    private function createInitializeAssign(string $variableName, string $methodName): Expression
    {
        $servicesVariable = new Variable($variableName);
        $containerConfiguratorVariable = new Variable(VariableName::CONTAINER_CONFIGURATOR);

        $assign = new Assign($servicesVariable, new MethodCall($containerConfiguratorVariable, $methodName));

        return new Expression($assign);
    }

    private function createInitializeNode(string $key, array $nodes): array
    {
        if ($key === YamlKey::SERVICES) {
            $nodes[] = $this->createInitializeAssign(VariableName::SERVICES, MethodName::SERVICES);
        }

        if ($key === YamlKey::PARAMETERS) {
            $nodes[] = $this->createInitializeAssign(VariableName::PARAMETERS, MethodName::PARAMETERS);
        }

        return $nodes;
    }
}
