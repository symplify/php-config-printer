<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Expr\MethodCall;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\Sorter\YamlArgumentSorter;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class DecoratesServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    /**
     * @var string
     */
    private const DECORATION_ON_INVALID = 'decoration_on_invalid';

    /**
     * @var string
     */
    private const DECORATION_INNER_NAME = 'decoration_inner_name';

    /**
     * @var string
     */
    private const DECORATION_PRIORITY = 'decoration_priority';

    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
        private YamlArgumentSorter $yamlArgumentSorter,
        private CommonNodeFactory $commonNodeFactory
    ) {
    }

    public function decorateServiceMethodCall(
        mixed $key,
        mixed $yaml,
        mixed $values,
        MethodCall $methodCall
    ): MethodCall {
        $arguments = $this->yamlArgumentSorter->sortArgumentsByKeyIfExists($values, [
            self::DECORATION_INNER_NAME => null,
            self::DECORATION_PRIORITY => 0,
            self::DECORATION_ON_INVALID => null,
        ]);

        if (isset($arguments[self::DECORATION_ON_INVALID])) {
            $arguments[self::DECORATION_ON_INVALID] = $arguments[self::DECORATION_ON_INVALID] === 'exception'
                ? $this->commonNodeFactory->createConstFetch(
                    ContainerInterface::class,
                    'EXCEPTION_ON_INVALID_REFERENCE'
                )
                : $this->commonNodeFactory->createConstFetch(
                    ContainerInterface::class,
                    'IGNORE_ON_INVALID_REFERENCE'
                );
        }

        // Don't write the next arguments if they are null.
        if ($arguments[self::DECORATION_ON_INVALID] === null && $arguments[self::DECORATION_PRIORITY] === 0) {
            unset($arguments[self::DECORATION_ON_INVALID], $arguments[self::DECORATION_PRIORITY]);

            if ($arguments[self::DECORATION_INNER_NAME] === null) {
                unset($arguments[self::DECORATION_INNER_NAME]);
            }
        }

        array_unshift($arguments, $values['decorates']);

        $args = $this->argsNodeFactory->createFromValues($arguments);
        return new MethodCall($methodCall, 'decorate', $args);
    }

    public function isMatch(mixed $key, mixed $values): bool
    {
        return $key === YamlServiceKey::DECORATES;
    }
}
