<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

use RuntimeException;

final class IntegerReplacer implements Replacer
{
    public const string SPECIFIER = "?d";

    #[\Override] public function replace(string $query, int $offset, string|array|null|int|float|bool $value): string
    {
        if (! is_int($value) && ! is_bool($value) && ! is_null($value)) {
            throw new RuntimeException(sprintf("Invalid argument type for replacer '%s'.", get_class($this)));
        }

        if (is_int($value) || is_bool($value)) {
            $value = (int) $value;
        }

        return sprintf(substr_replace($query, '%d', $offset, 2), $value);
    }
}