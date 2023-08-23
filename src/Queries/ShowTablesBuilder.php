<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\Interfaces\ShowTablesBuilder as ShowTablesBuilderInterface;
use Medas\PdoStorage\PdoStorageController;
use Medas\PdoStorage\Queries\Query;
use Medas\PdoStorage\Queries\QuerySet;

#[Service]
readonly class ShowTablesBuilder implements ShowTablesBuilderInterface
{
    public function __construct(
        private PdoStorageController $pdoStorageController,
    )
    {
    }

    public function build(Database $database, string $name): QuerySet
    {
        $quotedName = $this->pdoStorageController->quote($database, $name);

        return QuerySet::fromQuery(new Query('show tables like ' . $quotedName));
    }
}
