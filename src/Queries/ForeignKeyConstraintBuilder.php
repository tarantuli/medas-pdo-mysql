<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{Database, Drivers\DriverHandler};
use Medas\StorageManager\Structure\Blueprint\ForeignKey;

#[Service]
readonly class ForeignKeyConstraintBuilder
{
    public function buildAdd(
        string        $entityName,
        DriverHandler $driver,
        Database      $database,
        ForeignKey    $foreignKey
    ): string
    {
        return ' add constraint '
            . $driver->quote($database, $this->createForeignKeyName($entityName, $foreignKey))
            . "\n"
            . '  foreign key ('
            . $driver->quote($database, $foreignKey->field)
            . ")\n"
            . '  references '
            . $driver->quote($database, $foreignKey->foreignEntity)
            . ' ('
            . $driver->quote($database, $foreignKey->foreignField)
            . ")"
            . ($foreignKey->doCascade ? ' on delete cascade on update cascade' : '');
    }

    public function buildDrop(
        string        $entityName,
        DriverHandler $driver,
        Database      $database,
        ForeignKey    $foreignKey
    ): string
    {
        return ' drop constraint ' . $driver->quote($database, $this->createForeignKeyName($entityName, $foreignKey));
    }

    private function createForeignKeyName(string $entityName, ForeignKey $foreignKey): string
    {
        return sha1($entityName . "\n" . $foreignKey->field . "\n" . $foreignKey->foreignEntity . "\n" . $foreignKey->foreignField);
    }
}
