<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\Interfaces\ShowTablesBuilder as ShowTablesBuilderInterface;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\{Query, QuerySet};

#[Service]
readonly class ShowTablesBuilder implements ShowTablesBuilderInterface
{
    public function __construct(
        private PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(Database $database, string|null $name = null): QuerySet
    {
        if ($name === null) {
            return QuerySet::fromQuery(new Query('show tables', [], $database));
        }
        else {
            $escapedName = $this->pdoStorageController->escape($database, $name);

            return QuerySet::fromQuery(new Query('show tables like ' . $escapedName, [], $database));
        }
    }
}
