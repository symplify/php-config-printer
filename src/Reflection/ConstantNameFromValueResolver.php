<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Reflection;

use ReflectionClass;

final class ConstantNameFromValueResolver
{
    /**
     * @param class-string $class
     */
    public function resolveFromValueAndClass(int|string $constantValue, string $class): ?string
    {
        $reflectionClass = new ReflectionClass($class);

        /** @var array<string, mixed> $constants */
        $constants = $reflectionClass->getConstants();

        foreach ($constants as $name => $value) {
            if ($value === $constantValue) {
                return $name;
            }
        }

        return null;
    }
}
