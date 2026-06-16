<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{Database, Events\EscapeValueRequest};

#[Service]
readonly class ValueEscaper
{
    public function escape(Database $database, mixed $value): string
    {
        if (null === $value) {
            return 'null';
        }

        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        }

        if (is_bool($value)) {
            $value = (int) $value;
        }

        $request = dispatch(new EscapeValueRequest($database, $value));

        return $request->escapedValue;
    }
}
