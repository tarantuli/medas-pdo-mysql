<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Filters\{Between, LessThan, MoreThan};
use Medas\PdoStorage\Database;
use Medas\PdoStorage\PdoStorageController;

#[Service]
readonly class ConditionAppender
{
    public function __construct(
        public PdoStorageController $pdoStorageController,
    )
    {
    }

    public function append(Database $database, string &$query, array &$arguments, array $filters, string $separator = 'and'): void
    {
        $driverHandler = $this->pdoStorageController->getDatabaseController($database)->driverHandler;

        foreach ($filters as $field => $value) {
            if ($value instanceof LessThan) {
                $query .= $driverHandler->quote($database, $value->field) . ' < ? ' . $separator . ' ';
                $arguments[] = $value->value;
            }
            elseif ($value instanceof MoreThan) {
                $query .= $driverHandler->quote($database, $value->field) . ' > ? ' . $separator . ' ';
                $arguments[] = $value->value;
            }
            elseif ($value instanceof Between) {
                $query .= $driverHandler->quote($database, $value->field) . 'between ? and ? ' . $separator . ' ';
                $arguments[] = $value->lowerValue;
                $arguments[] = $value->upperValue;
            }
            else {
                if ($value === null && $separator === 'and') {
                    $query .= $driverHandler->quote($database, $field) . ' is null ' . $separator . ' ';
                }
                else {
                    $query .= $driverHandler->quote($database, $field) . ' = ? ' . $separator . ' ';
                    $arguments[] = $value;
                }
            }
        }

        $query = substr($query, 0, -2 - strlen($separator));
    }
}
