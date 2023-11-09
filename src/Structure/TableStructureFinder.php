<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Drivers\Interfaces\TableStructureFinder as TableStructureFinderInterface;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Table;
use Medas\StorageManager\Structure\{Blueprint, Blueprint\ForeignKey, Blueprint\Index};

#[Service]
readonly class TableStructureFinder implements TableStructureFinderInterface
{
    public function __construct(
        private PdoStorageController       $pdoStorageController,
        private DefinitionToFieldConverter $definitionToFieldConverter,
    )
    {
    }

    public function find(Table $table): Blueprint|null
    {
        $job = new TableStructureFinder\Job($table->storage(), $table);

        $job->createTable
            = $this->pdoStorageController->getDatabaseController($table->database)->driverHandler->tableStructureString($table);

        if ($job->createTable === null) {
            return null;
        }

        $this->findName($job);
        $this->findFields($job);
        $this->findPrimaryKey($job);
        $this->findKeys($job);
        $this->findForeignKeys($job);

        return $job->blueprint;
    }

    protected function findName(TableStructureFinder\Job $job): void
    {
        if (!preg_match('/create table `([^`]+)/i', $job->createTable, $match)) {
            return;
        }

        $job->blueprint->name = $match[1];
    }

    protected function findFields(TableStructureFinder\Job $job): void
    {
        if (!preg_match_all('/^ +`([^`]+)` (.+?),?$/m', $job->createTable, $matches, PREG_SET_ORDER)) {
            return;
        }

        foreach ($matches as $match) {
            $job->blueprint->addField($this->definitionToFieldConverter->convert($match[1], $match[2]));
        }
    }

    protected function findPrimaryKey(TableStructureFinder\Job $job): void
    {
        if (!preg_match('/primary key \(([^)]+)\)/i', $job->createTable, $match)) {
            return;
        }

        $index = new Index(isPrimary: true);

        foreach ($this->getNames($match[1]) as $name) {
            $index->addField($job->blueprint->fieldByName($name));
        }

        $index->isUnique = true;

        $job->blueprint->addIndex($index);
    }

    protected function getNames(string $nameString): array
    {
        $names = explode(',', $nameString);

        return array_map(fn($name) => trim($name, '`'), $names);
    }

    protected function findKeys(TableStructureFinder\Job $job): void
    {
        if (!preg_match_all(
            '/(?<isUnique>unique )?key `(?<name>[^`]+)` \((?<fields>[^)]+)\)/i',
            $job->createTable,
            $matches,
            PREG_SET_ORDER
        )) {
            return;
        }

        foreach ($matches as $match) {
            $index = new Index();

            foreach ($job->blueprint->fieldsByName($this->getNames($match['fields'])) as $field) {
                $index->addField($field);
            }

            $index->isUnique = isset($match['isUnique']);

            $job->blueprint->addIndex($index);
        }
    }

    protected function findForeignKeys(TableStructureFinder\Job $job): void
    {
        if (!preg_match_all(
            '/constraint `(?<name>[^`]+)` foreign key \(`(?<field>[^`]+)`\) references `(?<table>[^`]+)` \(`(?<reference>[^`]+)`\)(?<onDeleteCascade> on delete cascade)?/i',
            $job->createTable,
            $matches,
            PREG_SET_ORDER
        )) {
            return;
        }

        foreach ($matches as $match) {
            $foreignKey = new ForeignKey(
                $match['field'],
                $match['table'],
                $match['reference'],
                isset($match['onDeleteCascade'])
            );

            $job->blueprint->addForeignKey($foreignKey);
        }
    }
}
