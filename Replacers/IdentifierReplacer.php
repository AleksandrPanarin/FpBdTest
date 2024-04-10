<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

use RuntimeException;

final class IdentifierReplacer implements Replacer
{
    public const string SPECIFIER = "?#";

    #[\Override] public function replace(string $query, int $offset, string|array|null|int|float|bool $value): string
    {
        $insertValue = $value;
        if (! is_array($insertValue) && ! is_string($insertValue)) {
            throw new RuntimeException(sprintf("Invalid argument type for replacer '%s'.", get_class($this)));
        }

        if (is_string($insertValue)) {
            $insertValue = "`$insertValue`";
        }

        if (is_array($insertValue)) {
            $insertValue = implode(', ', array_map(fn (string $identifier): string => "`$identifier`", $insertValue));
        }

        return sprintf(substr_replace($query, '%s', $offset, 2), $insertValue);
    }
}