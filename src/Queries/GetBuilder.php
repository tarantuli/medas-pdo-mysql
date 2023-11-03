<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{PdoStorageController, Queries\Query, Queries\QuerySet};
use Medas\StorageManager\Interfaces\Builders\GetBuilder as GetBuilderInterface;
use Medas\StorageManager\UnitOfWork\ActionSet;

#[Service]
readonly class GetBuilder implements GetBuilderInterface
{
    public function __construct(
        public ConditionAppender    $conditionAppender,
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(array $stores, array $filters): ActionSet
    {
        $database = $stores[0]->storage();
        $query = 'select * from ';
        $arguments = [];

        foreach ($stores as $table) {
            $query .= $this->pdoStorageController->quote($database, $table->name) . ',';
        }

        $query = substr($query, 0, -1);

        if ($filters) {
            $query .= ' where ';
            $this->conditionAppender->append($database, $query, $arguments, $filters);
        }

        return QuerySet::fromQuery(new Query($query, $arguments, $database));
    }
}
