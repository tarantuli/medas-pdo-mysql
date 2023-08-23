<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Drivers\Interfaces\{QueryBuilders as QueryBuildersInterface, ShowTablesBuilder};
use Medas\StorageManager\Interfaces\Builders;

#[Service]
readonly class QueryBuilders implements QueryBuildersInterface
{
    public function createStore(): Builders\CreateStoreBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(CreateStoreBuilder::class);
    }

    public function selectorQuery(): Builders\SelectorActionBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(SelectorQueryBuilder::class);
    }

    public function insert(): Builders\InsertBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(InsertBuilder::class);
    }

    public function get(): Builders\GetBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(GetBuilder::class);
    }

    public function update(): Builders\UpdateBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(UpdateBuilder::class);
    }

    public function delete(): Builders\DeleteBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(DeleteBuilder::class);
    }

    public function collectionUpdate(): Builders\CollectionUpdateBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(CollectionUpdateBuilder::class);
    }

    public function showTables(): ShowTablesBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(ShowTablesBuilder::class);
    }
}
