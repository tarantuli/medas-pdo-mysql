<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure;

use Medas\Core\Attributes\Service;
use Medas\FileBuilder\PhpClass\MethodDefinition;
use Medas\PdoStorage\{PdoStorageController, Queries\Query};
use Medas\StorageManager\Interfaces\Storage;
use Medas\StorageManager\Migrations\MigrationBuilder as MigrationBuilderInterface;
use Medas\StorageManager\StorageManager;
use Medas\StorageManager\Structure\{Blueprint, Changes\ChangeFinder};
use Medas\StorageManager\UnitOfWork\{ActionSet, Priority};

#[Service]
readonly class MigrationBuilder implements MigrationBuilderInterface
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
        $queries = $this->buildActions($storage, $expectedStructure);

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
\$unitOfWork->addAction(new \\   $queryClass(
    <<<SQL
   $queryString
SQL,
    [],
    service(\\   $storageManagerClass::class)->byName("   $storageName"),
    \\   $priorityClass::{$query->priority()->name}

            
));
PHP;
        }

        return true;
    }

    public function buildActions(Storage $storage, Blueprint $blueprint): ActionSet
    {
        $driverHandler = $this->pdoStorageController->getDatabaseController($storage)->driverHandler;

        $existingStructure
            = $driverHandler->tableStructureFinder()->find($this->pdoStorageController->store($blueprint->name, $storage));

        if ($existingStructure === null) {
            return $this->createTableBuilder->create($storage, $blueprint);
        }
        else {
            $changes = $this->changeFinder->find($blueprint, $existingStructure);

            return $changes ? $this->alterTableBuilder->create($storage, $blueprint, $changes) : new ActionSet();
        }
    }
}
