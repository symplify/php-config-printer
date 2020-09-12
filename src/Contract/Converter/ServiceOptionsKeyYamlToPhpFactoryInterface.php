<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Contract\Converter;

use PhpParser\Node\Expr\MethodCall;

interface ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $serviceMethodCall): MethodCall;

    public function isMatch($key, $values): bool;
}
