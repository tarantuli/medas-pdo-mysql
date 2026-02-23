<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Drivers\Interfaces\{
    QueryBuilders as QueryBuildersInterface,
    ShowTablesBuilder
};
use Medas\StorageManager\Interfaces\Builders;

#[Service]
readonly class QueryBuilders implements QueryBuildersInterface
{
    public function __construct(
        private CollectionUpdateBuilder $collectionUpdateBuilder,
        private CreateStoreBuilder      $createStoreBuilder,
        private DeleteBuilder           $deleteBuilder,
        private DropTableBuilder        $dropTableBuilder,
        private GetBuilder              $getBuilder,
        private InsertBuilder           $insertBuilder,
        private SelectorQueryBuilder    $selectorQueryBuilder,
        private ShowTablesBuilder       $showTablesBuilder,
        private UpdateBuilder           $updateBuilder,
    )
    {
    }

    public function createStore(): Builders\CreateStoreBuilder
    {
        return $this->createStoreBuilder;
    }

    public function deleteStore(): Builders\DeleteStoreBuilder
    {
        return $this->dropTableBuilder;
    }

    public function selectorAction(): Builders\SelectorActionBuilder
    {
        return $this->selectorQueryBuilder;
    }

    public function insert(): Builders\InsertBuilder
    {
        return $this->insertBuilder;
    }

    public function get(): Builders\GetBuilder
    {
        return $this->getBuilder;
    }

    public function update(): Builders\UpdateBuilder
    {
        return $this->updateBuilder;
    }

    public function delete(): Builders\DeleteBuilder
    {
        return $this->deleteBuilder;
    }

    public function collectionUpdate(): Builders\CollectionUpdateBuilder
    {
        return $this->collectionUpdateBuilder;
    }

    public function showTables(): ShowTablesBuilder
    {
        return $this->showTablesBuilder;
    }
}
