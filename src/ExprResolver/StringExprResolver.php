<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ExprResolver;

use Nette\Utils\Strings;
use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\NodeFactory\ConstantNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\FunctionName;

final class StringExprResolver
{
    /**
     * @see https://regex101.com/r/laf2wR/1
     * @var string
     */
    private const TWIG_HTML_XML_SUFFIX_REGEX = '#\.(twig|html|xml)$#';

    public function __construct(
        private ConstantNodeFactory $constantNodeFactory,
        private CommonNodeFactory $commonNodeFactory
    ) {
    }

    public function resolve(
        string $value,
        bool $skipServiceReference,
        bool $skipClassesToConstantReference
    ): Expr {
        if ($value === '') {
            return new String_($value);
        }

        $constFetch = $this->constantNodeFactory->createConstantIfValue($value);
        if ($constFetch !== null) {
            return $constFetch;
        }

        // do not print "\n" as empty space, but use string value instead
        if (in_array($value, ["\r", "\n", "\r\n"], true)) {
            return $this->keepNewline($value);
        }

        $value = ltrim($value, '\\');
        if ($this->isClassType($value)) {
            return $this->resolveClassType($skipClassesToConstantReference, $value);
        }

        if (\str_starts_with($value, '@=')) {
            $value = ltrim($value, '@=');
            $expr = $this->resolve($value, $skipServiceReference, $skipClassesToConstantReference);
            $args = [new Arg($expr)];

            return new FuncCall(new FullyQualified(FunctionName::EXPR), $args);
        }

        // is service reference
        if (\str_starts_with($value, '@') && ! $this->isFilePath($value)) {
            return $this->resolveServiceReferenceExpr($value, $skipServiceReference, FunctionName::SERVICE);
        }

        return BuilderHelpers::normalizeValue($value);
    }

    private function keepNewline(string $value): String_
    {
        $string = new String_($value);
        $string->setAttribute(AttributeKey::KIND, String_::KIND_DOUBLE_QUOTED);

        return $string;
    }

    private function isFilePath(string $value): bool
    {
        return (bool) Strings::match($value, self::TWIG_HTML_XML_SUFFIX_REGEX);
    }

    private function resolveClassType(bool $skipClassesToConstantReference, string $value): String_ | ClassConstFetch
    {
        if ($skipClassesToConstantReference) {
            return new String_($value);
        }

        return $this->commonNodeFactory->createClassReference($value);
    }

    private function isClassType(string $value): bool
    {
        if (! ctype_upper($value[0])) {
            return false;
        }

        if (class_exists($value)) {
            return true;
        }

        return interface_exists($value);
    }

    /**
     * @param FunctionName::* $functionName
     */
    private function resolveServiceReferenceExpr(
        string $value,
        bool $skipServiceReference,
        string $functionName
    ): Expr {
        $value = ltrim($value, '@');
        $expr = $this->resolve($value, $skipServiceReference, false);

        if ($skipServiceReference) {
            return $expr;
        }

        $args = [new Arg($expr)];
        return new FuncCall(new FullyQualified($functionName), $args);
    }
}
