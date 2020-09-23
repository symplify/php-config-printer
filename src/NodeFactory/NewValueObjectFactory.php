<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\NodeFactory;

use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use ReflectionClass;

final class NewValueObjectFactory
{
    public function create(object $valueObject): New_
    {
        $valueObjectClass = get_class($valueObject);

        $propertyValues = $this->resolvePropertyValuesFromValueObject($valueObjectClass, $valueObject);
        $args = $this->createArgs($propertyValues);

        return new New_(new FullyQualified($valueObjectClass), $args);
    }

    /**
     * @return mixed[]
     */
    private function resolvePropertyValuesFromValueObject(string $valueObjectClass, object $valueObject): array
    {
        $reflectionClass = new ReflectionClass($valueObjectClass);
        $propertyValues = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            $propertyValues[] = $reflectionProperty->getValue($valueObject);
        }

        return $propertyValues;
    }

    /**
     * @param mixed[] $propertyValues
     * @return Arg[]
     */
    private function createArgs(array $propertyValues): array
    {
        $args = [];
        foreach ($propertyValues as $propertyValue) {
            $args[] = new Arg(BuilderHelpers::normalizeValue($propertyValue));
        }

        return $args;
    }
}
