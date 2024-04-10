<?php

declare(strict_types=1);

namespace FpDbTest\Replacers;

final class ReplacerFactory
{
    public static function createReplacer(string $specifier): Replacer
    {
        return match ($specifier) {
            ArrayValueReplacer::SPECIFIER => new ConditionalBlocksReplacer(new ArrayValueReplacer()),
            FloatReplacer::SPECIFIER =>  new ConditionalBlocksReplacer(new FloatReplacer()),
            IdentifierReplacer::SPECIFIER =>  new ConditionalBlocksReplacer(new IdentifierReplacer()),
            IntegerReplacer::SPECIFIER =>  new ConditionalBlocksReplacer(new IntegerReplacer()),
            SimpleReplacer::SPECIFIER =>  new ConditionalBlocksReplacer(new SimpleReplacer()),
        };
    }
}