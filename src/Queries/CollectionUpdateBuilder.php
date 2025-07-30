<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\{
    Attributes\ConfigValue,
    Attributes\Service,
    Interfaces\ManagedCollection,
    Types\Collection
};
use Medas\PdoStorage\ConfigOptions\JoinTables\TableNamingStrategy;
use Medas\PdoStorage\JoinTables\NamingStrategy;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\QuerySet;
use Medas\StorageManager\Interfaces\{
    Builders\CollectionUpdateBuilder as CollectionUpdateBuilderInterface,
    Store
};
use Medas\StorageManager\UnitOfWork\{ActionSet, Priority};

#[Service]
readonly class CollectionUpdateBuilder implements CollectionUpdateBuilderInterface
{
    public function __construct(
        private DeleteBuilder        $deleteBuilder,
        private InsertBuilder        $insertBuilder,
        private PdoStorageController $pdoStorageController,

        #[ConfigValue(TableNamingStrategy::class)]
        private NamingStrategy       $namingStrategy,
    )
    {
    }

    public function build(
        Store             $store,
        object            $entity,
        string            $name,
        Collection        $type,
        ManagedCollection $values
    ): ActionSet
    {
        $joinTable = $this->pdoStorageController->store(
            $this->namingStrategy->determine($store->name(), $name),
            $store->storage()
        );

        $queries = new QuerySet();

        foreach ($values->getAdditions() as $value) {
            foreach ($this->insertBuilder->build(
                $joinTable,
                ['id' => $entity, 'value' => $value],
                Priority::UpdateCollection
            ) as $query) {
                $queries[] = $query;
            }
        }

        foreach ($values->getDeletions() as $value) {
            foreach ($this->deleteBuilder->build(
                $joinTable,
                ['id' => $entity, 'value' => $value],
                Priority::UpdateCollection
            ) as $query) {
                $queries[] = $query;
            }
        }

        return $queries;
    }
}
