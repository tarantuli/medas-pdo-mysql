<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure\TableStructureFinder;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{PdoStorageController, Queries\Query, Queries\QueryExecutor, Table};

#[Service]
readonly class TableStructureStringFinder
{
    public function __construct(
        public QueryExecutor        $queryExecutor,
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function find(Table $table): string|null
    {
        if (!$this->pdoStorageController->hasStore($table)) {
            return null;
        }

        $quotedTable = $this->pdoStorageController->quote($table->database, $table->name);
        $query = new Query('show create table ' . $quotedTable, [], $table->database);

        $this->queryExecutor->execute($query);

        $data = $query->recordSet();

        return $data->hasRecords() ? $data->fetchRecord()['Create Table'] : null;
    }
}
