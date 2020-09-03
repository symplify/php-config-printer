<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Tests\YamlToPhpConverter;

use Migrify\PhpConfigPrinter\HttpKernel\PhpConfigPrinterKernel;
use Migrify\PhpConfigPrinter\YamlToPhpConverter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class YamlToPhpConverterTest extends AbstractKernelTestCase
{
    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;

    protected function setUp(): void
    {
        $this->bootKernel(PhpConfigPrinterKernel::class);
        $this->yamlToPhpConverter = self::$container->get(YamlToPhpConverter::class);
    }

    public function test(): void
    {
        $printedPhpConfigContent = $this->yamlToPhpConverter->convertYamlArray([
            'parameters' => [
                'key' => 'value',
            ],
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_parameters.php', $printedPhpConfigContent);
    }
}
