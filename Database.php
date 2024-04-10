<?php

namespace FpDbTest;

use Exception;
use FpDbTest\Replacers\ArrayValueReplacer;
use FpDbTest\Replacers\FloatReplacer;
use FpDbTest\Replacers\IdentifierReplacer;
use FpDbTest\Replacers\IntegerReplacer;
use FpDbTest\Replacers\ReplacerFactory;
use FpDbTest\Replacers\SimpleReplacer;
use mysqli;
use RuntimeException;

class Database implements DatabaseInterface
{
    private const array REPLACERS_SPECIFIERS = [
        ArrayValueReplacer::SPECIFIER,
        FloatReplacer::SPECIFIER,
        IdentifierReplacer::SPECIFIER,
        IntegerReplacer::SPECIFIER,
        SimpleReplacer::SPECIFIER,
    ];

    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function buildQuery(string $query, array $args = []): string
    {
        $args = array_values($args);

        if (str_contains($query, '?')) {
            $countSubstr = substr_count($query, '?');
            $countArgs = count($args);

            if ($countSubstr !== $countArgs) {
                throw new RuntimeException(
                    sprintf('Too many or too few arguments. Expected: %d, got: %d.', $countSubstr, $countArgs)
                );
            }

            for ($i = 0; $i < strlen($query); $i++) {
                $chars = substr($query, $i, 2);

                if (in_array($chars, self::REPLACERS_SPECIFIERS, true)) {
                    $arg = $this->prepareArg($args[0]);
                    $query = ReplacerFactory::createReplacer($chars)->replace($query, $i, $arg);

                    unset($args[0]);
                    $args = array_values($args);
                }
            }
        }

        return $query;
    }

    public function skip(): string
    {
        return IntegerReplacer::SPECIFIER;
    }

    private function prepareArg(string|array|null|int|float|bool $arg): string|array|null|int|float|bool
    {
        if (is_string($arg)) {
            return $this->mysqli->real_escape_string($arg);
        }

        if (is_array($arg)) {
            foreach ($arg as $key => $value) {
                $arg[$this->prepareArg($key)] = $this->prepareArg($value);
            }

            return $arg;
        }

        return $arg;
    }
}
