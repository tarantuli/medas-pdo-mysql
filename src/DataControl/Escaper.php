<?php

declare(strict_types=1);

namespace Medas\PdoMysql\DataControl;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{Database, PdoStorageController};

#[Service]
readonly class Escaper
{
    public function __construct(
        private PdoStorageController $pdoStorageController,
    )
    {
    }

    public function escape(Database $database, mixed $value): string
    {
        if (null === $value) {
            return 'null';
        }

        if (is_object($value) && enum_exists($value::class)) {
            $value = $value->value;
        }

        if (is_bool($value)) {
            $value = (int) $value;
        }

        return $this->pdoStorageController->getDatabaseController($database)->pdo->quote((string) $value);
    }
}
