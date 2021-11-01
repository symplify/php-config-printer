<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use Nette\Utils\Strings;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\PhpConfigPrinter\Dummy\YamlContentProvider;

/**
 * Hacking constants @solve better in the future now it's hardcoded very deep in yaml parser, so unable to detected:
 * https://github.com/symfony/symfony/blob/ba4d57bb5fc0e9a1b4f63ced66156296dea3687e/src/Symfony/Component/Yaml/Inline.php#L617
 *
 * @see https://github.com/symfony/symfony/pull/18626/files
 *
 * @see \Symplify\PhpConfigPrinter\Tests\NodeFactory\ConstantNodeFactoryTest
 */
final class ConstantNodeFactory
{
    public function __construct(
        private YamlContentProvider $yamlContentProvider
    ) {
    }

    /**
     * @return ConstFetch|ClassConstFetch|null
     */
    public function createConstantIfValue(string $value): ?Expr
    {
        if (\str_contains($value, '::')) {
            [$class, $constant] = explode('::', $value);

            // not uppercase → probably not a constant
            if (strtoupper($constant) !== $constant) {
                return null;
            }

            return new ClassConstFetch(new FullyQualified($class), $constant);
        }

        $definedConstants = get_defined_constants();

        foreach (array_keys($definedConstants) as $constantName) {
            $constantValue = $this->getConstantValueIgnoringDeprecationWarnings($constantName);
            if ($value !== $constantValue) {
                continue;
            }

            $yamlContent = $this->yamlContentProvider->getYamlContent();
            $constantDefinitionPattern = '#' . preg_quote('!php/const', '#') . '(\s)+' . $constantName . '#';

            if (! Strings::match($yamlContent, $constantDefinitionPattern)) {
                continue;
            }

            return new ConstFetch(new Name($constantName));
        }

        return null;
    }

    private function getConstantValueIgnoringDeprecationWarnings(string $constant): mixed
    {
        $previousLevel = error_reporting(E_ALL & ~E_DEPRECATED);

        $value = constant($constant);

        error_reporting($previousLevel);

        return $value;
    }
}
