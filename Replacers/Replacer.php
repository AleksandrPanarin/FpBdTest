<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

interface Replacer
{
    public function replace(string $query, int $offset, string|array|null|int|float|bool $value): string;
}
