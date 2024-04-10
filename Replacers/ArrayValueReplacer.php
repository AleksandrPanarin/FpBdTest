<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

use RuntimeException;

final class ArrayValueReplacer implements Replacer
{
    public const string SPECIFIER = "?a";

    #[\Override] public function replace(string $query, int $offset, string|array|null|int|float|bool $value): string
    {
        $insertValue = $value;
        if (! is_array($insertValue)) {
            throw new RuntimeException(sprintf("Invalid argument type for replacer '%s'.", get_class($this)));
        }

        if ($this->isAssociative($insertValue)) {
            $tmpArray = [];

            foreach ($insertValue as $key => $value) {
                if (
                    ! is_string($value) &&
                    ! is_int($value) &&
                    ! is_float($value) &&
                    ! is_bool($value) &&
                    ! is_null($value)
                ) {
                    throw new RuntimeException(sprintf("Invalid argument type for replacer data '%s'.", get_class($this)));
                }

                if (is_string($key) && ! is_null($value)) {
                    $tmpArray[] = sprintf("`%s` = '%s'", $key, $value);
                } elseif (is_null($value)) {
                    $tmpArray[] = sprintf("`%s` = NULL", $key);
                } else {
                    $tmpArray[] = sprintf("'%s'", $value);
                }
            }
            $insertValue = $tmpArray;
        }

        return sprintf(substr_replace($query, '%s', $offset, 2), implode(', ', $insertValue));
    }

    private function isAssociative(array $array): bool
    {
        return array_values($array) !== $array;
    }
}