<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Controllers;

use Medas\PdoStorage\Drivers\Bases\BaseCreateTableBuilder;
use Medas\PdoStorage\Drivers\Bases\BuildJob;
use Medas\StorageManager\Structure\Blueprint\{Field, Index};

class CreateTableBuilder extends BaseCreateTableBuilder
{
    private ForeignKeyConstraintBuilder $foreignKeyConstraintBuilder;

    protected function initialize(): void
    {
        $this->foreignKeyConstraintBuilder = new ForeignKeyConstraintBuilder();
    }

    protected function addKeys(BuildJob $job): void
    {
        foreach ($job->blueprint->indexes() as $index) {
            if ($index->isPrimary) {
                $job->baseQuery .= ' primary key (';
            }
            else {
                if ($index->isUnique) {
                    $job->baseQuery .= ' unique';
                }

                $job->baseQuery .= ' key ' . $this->driver->quote($this->createIndexName($index)) . ' (';
            }

            foreach ($index->fields() as $field) {
                $job->baseQuery .= $this->driver->quote($field->name) . ',';
            }

            $job->baseQuery = substr($job->baseQuery, 0, -1) . "),\n";
        }
    }

    private function createIndexName(Index $index): string
    {
        $names = array_map(fn(Field $field) => $field->name, $index->fields());

        return sha1((implode("\n", $names)));
    }

    protected function processForeignKeys(BuildJob $job): void
    {
        foreach ($job->blueprint->foreignKeys() as $foreignKey) {
            $job->foreignKeys[] = $this->foreignKeyConstraintBuilder
                ->buildAdd($job->blueprint->name(), $this->driver, $foreignKey);
        }
    }
}
