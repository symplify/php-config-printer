<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\ServiceOptionConverter;

use Migrify\MigrifyKernel\Exception\NotImplementedYetException;
use Migrify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use PhpParser\Node\Expr\MethodCall;

final class SharedPublicServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        if ($key === 'public') {
            if ($yaml === false) {
                return new MethodCall($methodCall, 'private');
            }

            return new MethodCall($methodCall, 'public');
        }

        throw new NotImplementedYetException();
    }

    public function isMatch($key, $values): bool
    {
        return in_array($key, ['shared', 'public'], true);
    }
}
