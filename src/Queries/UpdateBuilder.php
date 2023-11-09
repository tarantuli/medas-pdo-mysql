<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{PdoStorageController, Queries\Query, Queries\QuerySet};
use Medas\StorageManager\Interfaces\{Builders\UpdateBuilder as UpdateBuilderInterface, Store};
use Medas\StorageManager\UnitOfWork\{ActionSet, Priority};

#[Service]
readonly class UpdateBuilder implements UpdateBuilderInterface
{
    public function __construct(
        public ConditionAppender    $conditionAppender,
        public FieldAppender        $fieldAppender,
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(Store $store, array $updates, array $conditions): ActionSet
    {
        $arguments = [];
        $query = 'update ' . $this->pdoStorageController->quote($store->storage(), $store->name) . ' set ';

        $this->fieldAppender->append($store->storage(), $query, $arguments, $updates);

        $query .= ' where ';

        $this->conditionAppender->append($store->storage(), $query, $arguments, $conditions);

        return QuerySet::fromQuery(new Query($query, $arguments, $store->storage(), Priority::UpdateRecord));
    }
}
