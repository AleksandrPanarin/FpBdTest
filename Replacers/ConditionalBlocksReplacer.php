<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

use http\Exception\RuntimeException;

final class ConditionalBlocksReplacer implements Replacer
{
    private const string PATTERN = '/\{(?<statement>.*?)\}/';

    private const array SPECIFIERS = [
        ArrayValueReplacer::SPECIFIER,
        FloatReplacer::SPECIFIER,
        IdentifierReplacer::SPECIFIER,
        IntegerReplacer::SPECIFIER,
    ];

    private Replacer $replacer;

    public function __construct(Replacer $replacer)
    {
        $this->replacer = $replacer;
    }

    #[\Override] public function replace(string $query, int $offset, string|array|null|int|float|bool $value): string
    {
        if (preg_match(self::PATTERN, $query, $matches)) {
            $conditionalBlock = $matches[0];
            $startConditionalBlockPosition = strpos($query, $matches[0]);
            $endConditionalBlockPosition = $startConditionalBlockPosition + strlen($conditionalBlock);

            if ($offset > $startConditionalBlockPosition && $offset < $endConditionalBlockPosition) {
                $query = str_replace($conditionalBlock, '', $query);

                if (is_string($value) && in_array($value, self::SPECIFIERS, true)) {
                    return $query;
                }

                $query = substr_replace($query, $matches['statement'], $startConditionalBlockPosition);
                $offset = $this->changedOffset($query, $matches['statement']);
            }
        }

        return $this->replacer->replace($query, $offset, $value);
    }

    private function changedOffset(string $query, string $statement): int
    {
        $startConditionalBlockPosition = strpos($query, $statement);

        for ($offset = $startConditionalBlockPosition; $offset < strlen($query); $offset++) {
            $chars = substr($query, $offset, 2);
            if (in_array($chars, self::SPECIFIERS, true)) {
                return $offset;
            }
        }
        throw new RuntimeException('No specifier found in conditional block.');
    }
}