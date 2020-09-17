<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Dummy;

use Migrify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;

final class DummyYamlFileContentProvider implements YamlFileContentProviderInterface
{
    public function setContent(string $yamlContent): void
    {
    }

    public function getYamlContent(): string
    {
        return '';
    }
}
