<?php

declare(strict_types=1);

namespace Migrify\PhpConfigPrinter\ServiceOptionConverter;

use Migrify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Migrify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Migrify\PhpConfigPrinter\ValueObject\YamlKey;
use Migrify\PhpConfigPrinter\ValueObject\YamlServiceKey;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;

final class BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    /**
     * @var CommonNodeFactory
     */
    private $commonNodeFactory;

    public function __construct(CommonNodeFactory $commonNodeFactory)
    {
        $this->commonNodeFactory = $commonNodeFactory;
    }

    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        $method = $key;
        if ($key === 'shared') {
            $method = 'share';
        }

        $methodCall = new MethodCall($methodCall, $method);
        if ($yaml === false) {
            $methodCall->args[] = new Arg($this->commonNodeFactory->createFalse());
        }

        return $methodCall;
    }

    public function isMatch($key, $values): bool
    {
        return in_array($key, [YamlServiceKey::BIND, YamlKey::AUTOWIRE, YamlKey::AUTOCONFIGURE], true);
    }
}
