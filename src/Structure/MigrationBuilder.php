<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure;

use Medas\Core\Attributes\Service;
use Medas\FileBuilder\PhpClass\MethodDefinition;
use Medas\PdoStorage\Drivers\Interfaces\PdoMigrationBuilder;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\{Query, QuerySet};
use Medas\StorageManager\Interfaces\Storage;
use Medas\StorageManager\StorageManager;
use Medas\StorageManager\Structure\{Blueprint, Changes\ChangeFinder};
use Medas\StorageManager\UnitOfWork\Priority;

#[Service]
readonly class MigrationBuilder implements PdoMigrationBuilder
{
    public function __construct(
        private AlterTableBuilder    $alterTableBuilder,
        private ChangeFinder         $changeFinder,
        private CreateTableBuilder   $createTableBuilder,
        private PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(
        Storage          $storage,
        Blueprint        $expectedStructure,
        MethodDefinition $migrateMethod,
        MethodDefinition $undoMethod,
    ): bool
    {
        $queries = $this->buildQueries($storage, $expectedStructure);

        if (count($queries) === 0) {
            return false;
        }

        $queryClass = Query::class;
        $storageManagerClass = StorageManager::class;
        $priorityClass = Priority::class;

        foreach ($queries as $query) {
            $queryString = addcslashes(trim($query->query), '"');
            $storageName = addcslashes(trim($query->storage()->name()), '"');

            $migrateMethod->body .= <<<PHP
\$unitOfWork->addAction(new \\$queryClass(
    <<<SQL
$queryString
SQL,
    [],
    service(\\$storageManagerClass::class)->byName("$storageName"),
    \\$priorityClass::{$query->priority()->name}
));
PHP;
        }

        return true;
    }

    public function buildQueries(Storage $storage, Blueprint $expectedStructure): QuerySet|null
    {
        $driverHandler = $this->pdoStorageController->getDatabaseController($storage)->driverHandler;

        $existingStructure = $driverHandler->tableStructureFinder()->find($this->pdoStorageController->store($expectedStructure->name(), $storage));

        if ($existingStructure === null) {
            return $this->createTableBuilder->create($storage, $expectedStructure);
        }
        else {
            $changes = $this->changeFinder->find($expectedStructure, $existingStructure);
            return $changes ? $this->alterTableBuilder->create($storage, $expectedStructure, $changes) : null;
        }
    }
}
