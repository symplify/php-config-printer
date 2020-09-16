<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Dummy;

use Migrify\PhpConfigPrinter\Contract\SymfonyVersionFeatureGuardInterface;

final class DummySymfonyVersionFeatureGuard implements SymfonyVersionFeatureGuardInterface
{
    public function isAtLeastSymfonyVersion(float $symfonyVersion): bool
    {
        return true;
    }
}
