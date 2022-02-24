<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\Node;
use PhpParser\Node\Stmt\Return_;
use Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use Symplify\PhpConfigPrinter\PhpParser\NodeFactory\ConfiguratorClosureNodeFactory;

/**
 * @api
 */
final class RoutingConfiguratorReturnClosureFactory
{
    /**
     * @param RoutingCaseConverterInterface[] $routingCaseConverters
     */
    public function __construct(
        private ConfiguratorClosureNodeFactory $containerConfiguratorClosureNodeFactory,
        private array $routingCaseConverters
    ) {
    }

    /**
     * @param mixed[] $arrayData
     */
    public function createFromArrayData(array $arrayData): Return_
    {
        $stmts = $this->createClosureStmts($arrayData);
        $closure = $this->containerConfiguratorClosureNodeFactory->createRoutingClosureFromStmts($stmts);
        return new Return_($closure);
    }

    /**
     * @param mixed[] $arrayData
     * @return mixed[]
     */
    private function createClosureStmts(array $arrayData): array
    {
        $arrayData = $this->removeEmptyValues($arrayData);
        return $this->createNodesFromCaseConverters($arrayData);
    }

    /**
     * @param mixed[] $yamlData
     * @return mixed[]
     */
    private function removeEmptyValues(array $yamlData): array
    {
        return array_filter($yamlData);
    }

    /**
     * @param mixed[] $arrayData
     * @return Node[]
     */
    private function createNodesFromCaseConverters(array $arrayData): array
    {
        $nodes = [];

        foreach ($arrayData as $key => $values) {
            $expression = null;

            foreach ($this->routingCaseConverters as $routingCaseConverter) {
                if (! $routingCaseConverter->match($key, $values)) {
                    continue;
                }

                $expression = $routingCaseConverter->convertToMethodCall($key, $values);
                break;
            }

            if ($expression === null) {
                continue;
            }

            $nodes[] = $expression;
        }

        return $nodes;
    }
}
