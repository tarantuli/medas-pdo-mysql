<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\EntityManager\MetaData\Property;
use Medas\PdoStorage\ConfigOptions\JoinTables\TableNamingStrategy;
use Medas\PdoStorage\JoinTables\NamingStrategy;
use Medas\PdoStorage\PdoStorageController;
use Medas\StorageManager\Interfaces\Fetchers\CollectionRecordFetcher as CollectionRecordFetcherInterface;
use Medas\StorageManager\Interfaces\Store;

#[Service]
readonly class CollectionRecordFetcher implements CollectionRecordFetcherInterface
{
    public function __construct(
        public FilteredFetcher      $filteredFetcher,
        public PdoStorageController $pdoStorageController,

        #[ConfigValue(TableNamingStrategy::class)]
        private NamingStrategy $namingStrategy,
    )
    {
    }

    public function fetch(Store $store, object $entity, Property $property): iterable
    {
        $joinTableName = $this->namingStrategy->determine($store->name(), $property->name);
        $joinTable = $this->pdoStorageController->store($joinTableName, $store->storage());

        return $this->filteredFetcher->fetch($joinTable, ['id' => $entity])->fetchRecords();
    }
}
