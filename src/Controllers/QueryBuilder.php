<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Controllers;

use Medas\PdoStorage\Drivers\Bases\BaseQueryBuilder;
use Medas\PdoStorage\Queries\Query;
use Medas\PdoStorage\Queries\QueryCollection;

class QueryBuilder extends BaseQueryBuilder
{
    public function showTables(?string $name): QueryCollection
    {
        return QueryCollection::fromQuery(new Query('show tables like "' . $name . '"'));
    }
}
