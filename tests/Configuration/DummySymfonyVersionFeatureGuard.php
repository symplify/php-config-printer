<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Tests\Configuration;

use Migrify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;

final class DummySymfonyVersionFeatureGuard implements SymfonyVersionFeatureGuardInterface
{
    public function isAtLeastSymfonyVersion(float $symfonyVersion): bool
    {
        return true;
    }
}
