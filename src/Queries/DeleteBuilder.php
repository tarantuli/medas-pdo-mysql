<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\Query;
use Medas\PdoStorage\Queries\QuerySet;
use Medas\StorageManager\Interfaces\Builders\DeleteBuilder as DeleteBuilderInterface;
use Medas\StorageManager\Interfaces\Store;
use Medas\StorageManager\UnitOfWork\ActionSet;
use Medas\StorageManager\UnitOfWork\Priority;

#[Service]
readonly class DeleteBuilder implements DeleteBuilderInterface
{
    public function __construct(
        public ConditionAppender    $conditionAppender,
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(Store $store, array $conditions, Priority $priority = Priority::DeleteRecord): ActionSet
    {
        $arguments = [];

        $query = 'delete from ' . $this->pdoStorageController->quote($store->storage(), $store->name) . ' where ';
        $this->conditionAppender->append($store->storage(), $query, $arguments, $conditions);

        return QuerySet::fromQuery(new Query($query, $arguments, $store->storage(), $priority));
    }
}
