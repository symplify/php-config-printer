<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symplify\PhpConfigPrinter\Exception\NotImplementedYetException;
use Symplify\PhpConfigPrinter\ExprResolver\StringExprResolver;
use Symplify\PhpConfigPrinter\ExprResolver\TaggedReturnsCloneResolver;
use Symplify\PhpConfigPrinter\ExprResolver\TaggedServiceResolver;
use Symplify\PhpConfigPrinter\ValueObject\FunctionName;

final class ArgsNodeFactory
{
    /**
     * @var string
     */
    private const TAG_SERVICE = 'service';

    /**
     * @var string
     */
    private const TAG_RETURNS_CLONE = 'returns_clone';

    private bool $isPhpNamedArguments = false;

    public function __construct(
        private StringExprResolver $stringExprResolver,
        private TaggedReturnsCloneResolver $taggedReturnsCloneResolver,
        private TaggedServiceResolver $taggedServiceResolver,
        private NewValueObjectFactory $newValueObjectFactory,
    ) {
        $this->isPhpNamedArguments = PHP_VERSION_ID >= 80000;
    }

    /**
     * @return Arg[]
     */
    public function createFromValuesAndWrapInArray(mixed $values): array
    {
        if (is_array($values)) {
            $array = $this->resolveExprFromArray($values);
        } else {
            $expr = $this->resolveExpr($values);
            $items = [new ArrayItem($expr)];
            $array = new Array_($items);
        }

        return [new Arg($array)];
    }

    /**
     * @return mixed[]|Arg[]
     */
    public function createFromValues(
        mixed $values,
        bool $skipServiceReference = false,
        bool $skipClassesToConstantReference = false,
        bool $isForConfig = false
    ): array {
        if (is_array($values)) {
            $args = [];
            foreach ($values as $key => $value) {
                $expr = $this->resolveExpr($value, $skipServiceReference, $skipClassesToConstantReference);
                $args = $this->resolveArgs($args, $key, $expr, $isForConfig);
            }

            return $args;
        }

        if ($values instanceof Node) {
            if ($values instanceof Arg) {
                return [$values];
            }

            if ($values instanceof Expr) {
                return [new Arg($values)];
            }
        }

        if (is_string($values)) {
            $expr = $this->resolveExpr($values);
            return [new Arg($expr)];
        }

        throw new NotImplementedYetException();
    }

    public function resolveExpr(
        mixed $value,
        bool $skipServiceReference = false,
        bool $skipClassesToConstantReference = false
    ): Expr {
        if (is_string($value)) {
            return $this->stringExprResolver->resolve(
                $value,
                $skipServiceReference,
                $skipClassesToConstantReference
            );
        }

        if ($value instanceof Expr) {
            return $value;
        }

        if ($value instanceof TaggedValue) {
            return $this->createServiceReferenceFromTaggedValue($value);
        }

        if (is_array($value)) {
            $arrayItems = $this->resolveArrayItems($value, $skipClassesToConstantReference);
            return new Array_($arrayItems);
        }

        if (is_object($value)) {
            return $this->newValueObjectFactory->create($value);
        }

        return BuilderHelpers::normalizeValue($value);
    }

    /**
     * @param mixed[] $values
     */
    public function resolveExprFromArray(array $values): Array_
    {
        $arrayItems = [];
        foreach ($values as $key => $value) {
            $expr = is_array($value) ? $this->resolveExprFromArray($value) : $this->resolveExpr($value);

            if (! is_int($key)) {
                $keyExpr = $this->resolveExpr($key);
                $arrayItem = new ArrayItem($expr, $keyExpr);
            } else {
                $arrayItem = new ArrayItem($expr);
            }

            $arrayItems[] = $arrayItem;
        }

        return new Array_($arrayItems);
    }

    /**
     * @param Arg[] $args
     * @return Arg[]
     */
    private function resolveArgs(array $args, mixed $key, Expr $expr, bool $isForConfig): array
    {
        if (is_string($key) && $isForConfig) {
            $key = $this->resolveExpr($key);
            $args[] = new Arg(new ArrayItem($expr, $key));

            return $args;
        }

        if (! is_int($key) && $this->isPhpNamedArguments) {
            $args[] = new Arg($expr, false, false, [], new Identifier($key));

            return $args;
        }

        $args[] = new Arg($expr);
        return $args;
    }

    private function createServiceReferenceFromTaggedValue(TaggedValue $taggedValue): Expr
    {
        // that's the only value
        if ($taggedValue->getTag() === self::TAG_RETURNS_CLONE) {
            return $this->taggedReturnsCloneResolver->resolve($taggedValue);
        }

        if ($taggedValue->getTag() === self::TAG_SERVICE) {
            return $this->taggedServiceResolver->resolve($taggedValue);
        }

        $name = match ($taggedValue->getTag()) {
            'tagged_iterator' => new FullyQualified(FunctionName::TAGGED_ITERATOR),
            'tagged_locator' => new FullyQualified(FunctionName::TAGGED_LOCATOR),
            default => new Name($taggedValue->getTag())
        };

        $args = $this->createFromValues($taggedValue->getValue());

        return new FuncCall($name, $args);
    }

    /**
     * @param mixed[] $value
     * @return ArrayItem[]
     */
    private function resolveArrayItems(array $value, bool $skipClassesToConstantReference): array
    {
        $arrayItems = [];

        $naturalKey = 0;
        foreach ($value as $nestedKey => $nestedValue) {
            $valueExpr = $this->resolveExpr($nestedValue, false, $skipClassesToConstantReference);

            if (! is_int($nestedKey) || $nestedKey !== $naturalKey) {
                $keyExpr = $this->resolveExpr($nestedKey, false, $skipClassesToConstantReference);
                $arrayItem = new ArrayItem($valueExpr, $keyExpr);
            } else {
                $arrayItem = new ArrayItem($valueExpr);
            }

            $arrayItems[] = $arrayItem;

            ++$naturalKey;
        }

        return $arrayItems;
    }
}
