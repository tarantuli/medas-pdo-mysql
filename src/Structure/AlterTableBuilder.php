<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure;

use Medas\Core\Attributes\Service;
use Medas\PdoMysql\Queries\ForeignKeyConstraintBuilder;
use Medas\PdoStorage\Database;
use Medas\PdoStorage\JoinTableManager;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\Query;
use Medas\PdoStorage\Queries\QuerySet;
use Medas\StorageManager\Structure\Blueprint;
use Medas\StorageManager\Structure\Blueprint\Type;
use Medas\StorageManager\Structure\Changes\Changes;
use Medas\StorageManager\UnitOfWork\Priority;

#[Service]
readonly class AlterTableBuilder
{
    public function __construct(
        private ForeignKeyConstraintBuilder $foreignKeyConstraintBuilder,
        private JoinTableManager            $joinTableManager,
        private PdoStorageController        $pdoStorageController,
    )
    {
    }

    public function create(Database $database, Blueprint $blueprint, Changes $changes): QuerySet
    {
        $job = new TableBuilders\Job(
            $database,
            $this->pdoStorageController->getDatabaseController($database)->driverHandler,
            $blueprint,
        );

        $job->changes = $changes;

        $this->processFields($job);
        $this->processIndexes($job);
        $this->processForeignKeys($job);

        if ($job->baseQuery !== null) {
            $job->querySet[] = new Query(
                substr($job->baseQuery, 0, -2),
                [],
                $job->database,
                Priority::AlterStore
            );
        }

        if ($job->dropForeignKeysQuery !== null) {
            $job->querySet[] = new Query(
                substr($job->dropForeignKeysQuery, 0, -2),
                [],
                $job->database,
                Priority::DeleteStoreRelations
            );
        }

        if ($job->addForeignKeysQuery !== null) {
            $job->querySet[] = new Query(
                substr($job->addForeignKeysQuery, 0, -2),
                [],
                $job->database,
                Priority::AddStoreRelations
            );
        }

        $this->processCollections($job);

        return $job->querySet;
    }

    private function processFields(TableBuilders\Job $job): void
    {
        foreach ($job->changes->addFields as $field) {
            if ($field->type === Type::Collection) {
                $job->collections[] = $field;
                continue;
            }

            $definition = $job->driverHandler->fieldHandler()->buildDefinition($job->database, $field);

            if ($definition !== null) {
                if ($job->baseQuery === null) {
                    $job->baseQuery = $this->startAlterQuery($job);
                }
                $job->baseQuery .= sprintf(
                    "add column %s %s,\n",
                    $job->driverHandler->quote($job->database, $field->name),
                    $definition,
                );
            }
        }

        foreach ($job->changes->changeFields as $field) {
            $definition = $job->driverHandler->fieldHandler()->buildDefinition($job->database, $field);

            if ($definition !== null) {
                if ($job->baseQuery === null) {
                    $job->baseQuery = $this->startAlterQuery($job);
                }
                $job->baseQuery .= sprintf(
                    "modify column %1\$s %2\$s,\n",
                    $job->driverHandler->quote($job->database, $field->name),
                    $definition,
                );
            }
        }
    }

    private function startAlterQuery(TableBuilders\Job $job): string
    {
        return 'alter table ' . $job->driverHandler->quote($job->database, $job->changes->name) . "\n";
    }

    private function processIndexes(TableBuilders\Job $job): void
    {
        // TODO need to be implemented
    }

    private function processForeignKeys(TableBuilders\Job $job): void
    {
        if (!$job->changes->changeForeignKey && !$job->changes->addForeignKey) {
            return;
        }

        if ($job->changes->changeForeignKey) {
            $job->dropForeignKeysQuery = $this->startAlterQuery($job);

            foreach ($job->changes->changeForeignKey as $foreignKey) {
                $job->dropForeignKeysQuery .= $this->foreignKeyConstraintBuilder
                        ->buildDrop($job->changes->name, $job->driverHandler, $job->database, $foreignKey) . ",\n";
            }
        }

        $job->addForeignKeysQuery = $this->startAlterQuery($job);
        $foreignKeys = array_merge($job->changes->changeForeignKey, $job->changes->addForeignKey);

        foreach ($foreignKeys as $foreignKey) {
            $job->addForeignKeysQuery .= $this->foreignKeyConstraintBuilder
                    ->buildAdd($job->changes->name, $job->driverHandler, $job->database, $foreignKey) . ",\n";
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
                    $job->querySet[] = $query;
                }
            }
        }
    }
}
