<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{Database, Events\DatabaseControllerRequest};

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
            return (string) (int) $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        $request = dispatch(new DatabaseControllerRequest($database));

        return $request->databaseController->pdo->quote((string) $value);
    }
}
