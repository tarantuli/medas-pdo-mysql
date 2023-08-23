<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\Query;
use Medas\PdoStorage\Queries\QuerySet;
use Medas\StorageManager\Interfaces\Builders\DeleteStoreBuilder;
use Medas\StorageManager\Interfaces\Store;
use Medas\StorageManager\UnitOfWork\ActionSet;

#[Service]
readonly class DropTableBuilder implements DeleteStoreBuilder
{
    public function __construct(
        private PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(Store $store): ActionSet
    {
        $query = new Query(
            'drop table if exists ' . $this->pdoStorageController->quote($store->storage(), $store->name()),
            [],
            $store->storage()
        );

        return QuerySet::fromQuery($query);
    }
}
