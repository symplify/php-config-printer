<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source;

final class ClassWithConstants
{
    /**
     * @var string
     */
    public const CONFIG_KEY = 'config_value';

    /**
     * @var int
     */
    public const NUMERIC_CONFIG_KEY = 1200;
}
