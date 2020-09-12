<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Contract;

interface YamlFileContentProviderInterface
{
    public function setContent(string $yamlContent): void;

    public function getYamlContent(): string;
}
