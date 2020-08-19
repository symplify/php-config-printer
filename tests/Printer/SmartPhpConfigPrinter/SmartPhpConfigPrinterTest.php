<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter;

use Migrify\PhpConfigPrinter\HttpKernel\PhpConfigPrinterKernel;
use Migrify\PhpConfigPrinter\Printer\SmartPhpConfigPrinter;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\FirstClass;
use Migrify\PhpConfigPrinter\Tests\Printer\SmartPhpConfigPrinter\Source\SecondClass;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class SmartPhpConfigPrinterTest extends AbstractKernelTestCase
{
    /**
     * @var SmartPhpConfigPrinter
     */
    private $smartPhpConfigPrinter;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(PhpConfigPrinterKernel::class, [__DIR__ . '/../../config/config.tests.php']);

        $this->smartPhpConfigPrinter = self::$container->get(SmartPhpConfigPrinter::class);
    }

    public function test(): void
    {
        $printedContent = $this->smartPhpConfigPrinter->printConfiguredServices([
            FirstClass::class => [
                'some_key' => 'some_value',
            ],
            SecondClass::class => null,
        ]);

        $this->assertStringEqualsFile(__DIR__ . '/Fixture/expected_file.php', $printedContent);
    }
}
