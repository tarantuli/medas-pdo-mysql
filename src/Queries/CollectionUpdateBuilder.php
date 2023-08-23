<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\ManagedCollection;
use Medas\EntityManager\Types\Collection;
use Medas\PdoStorage\JoinTableManager;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\QuerySet;
use Medas\StorageManager\Interfaces\{Builders\CollectionUpdateBuilder as CollectionUpdateBuilderInterface, Store};
use Medas\StorageManager\UnitOfWork\{ActionSet, Priority};

#[Service]
readonly class CollectionUpdateBuilder implements CollectionUpdateBuilderInterface
{
    public function __construct(
        public DeleteBuilder        $deleteBuilder,
        public InsertBuilder        $insertBuilder,
        public JoinTableManager     $joinTableManager,
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(Store $store, object $entity, string $name, Collection $type, ManagedCollection $values): ActionSet
    {
        $joinTable = $this->pdoStorageController->store(
            $this->joinTableManager->determineName($store->name(), $name),
            $store->storage()
        );

        $queries = new QuerySet();

        foreach ($values->getAdditions() as $value) {
            foreach ($this->insertBuilder->build($joinTable, ['id' => $entity, 'value' => $value], Priority::UpdateCollection) as $query) {
                $queries[] = $query;
            }
        }

        foreach ($values->getDeletions() as $value) {
            foreach ($this->deleteBuilder->build($joinTable, ['id' => $entity, 'value' => $value], Priority::UpdateCollection) as $query) {
                $queries[] = $query;
            }
        }

        return $queries;
    }
}
