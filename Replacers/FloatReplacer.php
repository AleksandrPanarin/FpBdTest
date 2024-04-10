<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

use RuntimeException;

final class FloatReplacer implements Replacer
{
    public const string SPECIFIER = "?f";

    #[\Override] public function replace(string $query, int $offset,string|array|null|int|float|bool $value): string
    {
        if (! is_float($value) && ! is_bool($value) && ! is_null($value)) {
            throw new RuntimeException(sprintf("Invalid argument type for replacer '%s'.", get_class($this)));
        }

        if (is_float($value) || is_bool($value)) {
            $value = (float) $value;
        }

        return sprintf(substr_replace($query, '%f', $offset, 2), $value);
    }
}