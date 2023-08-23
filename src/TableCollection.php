<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\PdoStorage\{Database, Table};

class TableCollection
{
    private array $tables = [];

    public function get(Database $database, string $tableName): Table
    {
        $databaseName = $database->name();

        if (!isset($this->tables[$databaseName])) {
            $this->tables[$databaseName] = [];
        }

        if (!isset($this->tables[$databaseName][$tableName])) {
            $this->tables[$databaseName][$tableName] = new Table($database, $tableName);
        }

        return $this->tables[$databaseName][$tableName];
    }
}
