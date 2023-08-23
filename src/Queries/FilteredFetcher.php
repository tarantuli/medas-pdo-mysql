<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\Query;
use Medas\StorageManager\Interfaces\{Fetchers\FilteredFetcher as FilteredFetcherInterface, Record, RecordSet, Store};
use Medas\StorageManager\UnitOfWork\ActionExecutor;

#[Service]
readonly class FilteredFetcher implements FilteredFetcherInterface
{
    public function __construct(
        private PdoStorageController $pdoStorageController,
        private ActionExecutor       $actionExecutor,
    )
    {
    }

    public function fetch(Store $store, array $filters): RecordSet
    {
        /** @var Query $query */
        $query = $this->pdoStorageController->getDatabaseController($store->storage())->driverHandler->queryBuilders()->get()->build([$store], $filters)[0];
        $this->actionExecutor->execute($query);

        return $query->recordSet();
    }

    public function fetchOne(Store $store, array $filters): Record|null
    {
        return $this->fetch($store, $filters)->fetchRecord();
    }
}
