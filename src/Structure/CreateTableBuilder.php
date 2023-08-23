<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure;

use Medas\Core\Attributes\Service;
use Medas\PdoMysql\Queries\ForeignKeyConstraintBuilder;
use Medas\PdoStorage\Database;
use Medas\PdoStorage\JoinTableManager;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\{Query, QuerySet};
use Medas\StorageManager\Structure\{Blueprint, Blueprint\Field, Blueprint\Index};
use Medas\StorageManager\UnitOfWork\Priority;

#[Service]
readonly class CreateTableBuilder
{
    public function __construct(
        private ForeignKeyConstraintBuilder $foreignKeyConstraintBuilder,
        private JoinTableManager            $joinTableManager,
        private PdoStorageController        $pdoStorageController,
    )
    {
    }

    public function create(Database $database, Blueprint $blueprint): QuerySet
    {
        $job = new TableBuilders\Job(
            $database,
            $this->pdoStorageController->getDatabaseController($database)->driverHandler,
            $blueprint,
        );

        $tableName = $job->driverHandler->quote($database, $job->blueprint->name());

        $job->baseQuery = sprintf(/** @lang text */ "create table %s (\n", $tableName);

        $this->addFields($job);
        $this->addKeys($job);
        $this->processForeignKeys($job);

        $job->baseQuery = substr($job->baseQuery, 0, -2);
        $job->baseQuery .= "\n)\n";

        $job->QuerySet[] = new Query(
            query: $job->baseQuery,
            arguments: [],
            database: $job->database,
            priority: Priority::CreateStore
        );

        if ($job->foreignKeys) {
            $query = sprintf(
                "alter table %s\n%s",
                $tableName,
                implode(",\n", $job->foreignKeys)
            );

            $job->QuerySet[] = new Query(
                query: $query,
                arguments: [],
                database: $job->database,
                priority: Priority::AddStoreRelations
            );
        }

        $this->processCollections($job);

        return $job->QuerySet;
    }

    protected function addFields(TableBuilders\Job $job): void
    {
        foreach ($job->blueprint->fields() as $field) {
            if ($field->store !== null && $field->store !== $job->blueprint->name()) {
                // If this is the primary key, add it without generating value
                $primaryIndex = $job->blueprint->primaryIndex();

                if ($primaryIndex && in_array($field, $primaryIndex->fields())) {
                    $foreignKey = new Blueprint\ForeignKey($field->name, $field->store, $field->name, true);

                    $job->blueprint->addForeignKey($foreignKey);
                    $field->isGenerated = false;
                }
                else {
                    continue;
                }
            }

            if ($field->type === Blueprint\Type::Collection) {
                $job->collections[] = $field;
                continue;
            }

            $definition = $job->driverHandler->fieldHandler()->buildDefinition($job->database, $field);

            if ($definition !== null) {
                $job->baseQuery .= sprintf(
                    " %s %s,\n",
                    $job->driverHandler->quote($job->database, $field->name),
                    $definition,
                );
            }
        }
    }

    protected function processCollections(TableBuilders\Job $job): void
    {
        if (!$job->collections) {
            return;
        }

        foreach ($job->collections as $collectionField) {
            $queries = $this->joinTableManager->createQueries($job->database, $job->blueprint, $collectionField);

            if ($queries) {
                foreach ($queries as $query) {
                    $job->QuerySet[] = $query;
                }
            }
        }
    }

    protected function addKeys(TableBuilders\Job $job): void
    {
        foreach ($job->blueprint->indexes() as $index) {
            foreach ($index->fields() as $field) {
                if ($field->store !== null && $field->store !== $job->blueprint->name()) {
                    // If this is the primary key, do add it
                    $primaryIndex = $job->blueprint->primaryIndex();
                    if ($primaryIndex && in_array($field, $job->blueprint->primaryIndex()->fields())) {
                        // Do nothing
                    }
                    else {
                        continue 2;
                    }
                }
            }

            if ($index->isPrimary) {
                $job->baseQuery .= ' primary key (';
            }
            else {
                if ($index->isUnique) {
                    $job->baseQuery .= ' unique';
                }

                $job->baseQuery .= ' key ' . $job->driverHandler->quote($job->database, $this->createIndexName($index)) . ' (';
            }

            foreach ($index->fields() as $field) {
                $job->baseQuery .= $job->driverHandler->quote($job->database, $field->name) . ',';
            }

            $job->baseQuery = substr($job->baseQuery, 0, -1) . "),\n";
        }
    }

    private function createIndexName(Index $index): string
    {
        $names = array_map(fn(Field $field) => $field->name, $index->fields());

        return sha1((implode("\n", $names)));
    }

    protected function processForeignKeys(TableBuilders\Job $job): void
    {
        foreach ($job->blueprint->foreignKeys() as $foreignKey) {
            $job->foreignKeys[] = $this->foreignKeyConstraintBuilder
                ->buildAdd($job->blueprint->name(), $job->driverHandler, $job->database, $foreignKey);
        }
    }
}
