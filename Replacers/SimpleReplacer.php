<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

use RuntimeException;

final class SimpleReplacer implements Replacer
{
    public const string SPECIFIER = "?\x20";

    #[\Override] public function replace(string $query, int $offset, string|array|null|int|float|bool $value): string
    {
        return sprintf(substr_replace($query, "'%s'", $offset, 1), $value);
    }
}