<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure\TableStructureFinder;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\Query;
use Medas\PdoStorage\Table;
use Medas\StorageManager\UnitOfWork\ActionExecutor;

#[Service]
readonly class TableStructureStringFinder
{
    public function __construct(
        public ActionExecutor       $actionExecutor,
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function find(Table $table): string|null
    {
        $quotedTable = $this->pdoStorageController->quote($table->database, $table->name);
        $query = new Query('show create table ' . $quotedTable, [], $table->database);
        $this->actionExecutor->execute($query);

        $data = $query->recordSet();

        return $data->hasRecords() ? $data->fetchRecord()['Create Table'] : null;
    }
}
