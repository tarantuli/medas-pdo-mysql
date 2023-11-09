<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\PdoStorage\Table;
use Medas\StorageManager\{Interfaces\Storage, Interfaces\Store, Shared\StoreCollection};

/** @extends StoreCollection<Table> */
class TableCollection extends StoreCollection
{
    protected function createStore(Storage $storage, string $storeName): Store
    {
        return new Table($storage, $storeName);
    }
}
