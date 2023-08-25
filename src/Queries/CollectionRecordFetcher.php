<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\MetaData\Property;
use Medas\PdoStorage\JoinTableManager;
use Medas\PdoStorage\PdoStorageController;
use Medas\StorageManager\Interfaces\Fetchers\CollectionRecordFetcher as CollectionRecordFetcherInterface;
use Medas\StorageManager\Interfaces\Store;

#[Service]
readonly class CollectionRecordFetcher implements CollectionRecordFetcherInterface
{
    public function __construct(
        public JoinTableManager     $joinTableManager,
        public FilteredFetcher      $filteredFetcher,
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function fetch(Store $store, object $entity, Property $property): iterable
    {
        $joinTableName = $this->joinTableManager->determineName($store->name(), $property->name);
        $joinTable = $this->pdoStorageController->store($joinTableName, $store->storage());

        return $this->filteredFetcher->fetch($joinTable, ['id' => $entity])->fetchRecords();
    }
}
