<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Drivers\Interfaces\{
    DeleteStoreBuilder as DeleteStoreBuilderInterface,
    QueryBuilders as QueryBuildersInterface,
    ShowTablesBuilder
};
use Medas\PdoStorage\Queries\Builders as PdoStorageBuilders;
use Medas\StorageManager\Interfaces\Builders;

#[Service]
readonly class QueryBuilders implements QueryBuildersInterface
{
    public function __construct(
        private PdoStorageBuilders\CollectionUpdateBuilder $collectionUpdateBuilder,
        private PdoStorageBuilders\DeleteBuilder           $deleteBuilder,
        private PdoStorageBuilders\DropTableBuilder        $dropTableBuilder,
        private PdoStorageBuilders\GetBuilder              $getBuilder,
        private PdoStorageBuilders\InsertBuilder           $insertBuilder,
        private PdoStorageBuilders\UpdateBuilder           $updateBuilder,
        private SelectorQueryBuilder                       $selectorQueryBuilder,
        private ShowTablesBuilder                          $showTablesBuilder,
    )
    {
    }

    public function deleteStore(): DeleteStoreBuilderInterface
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
