<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\Interfaces\ShowTablesBuilder as ShowTablesBuilderInterface;
use Medas\PdoStorage\Events\EscapeValueRequest;
use Medas\PdoStorage\Queries\{Query, QuerySet};

#[Service]
readonly class ShowTablesBuilder implements ShowTablesBuilderInterface
{
    public function build(Database $database, string|null $name = null): QuerySet
    {
        if ($name === null) {
            return QuerySet::fromQuery(new Query('show tables', [], $database));
        }

        $request = dispatch(new EscapeValueRequest($database, $this->escapeLike($name)));

        return QuerySet::fromQuery(new Query('show tables like ' . $request->escapedValue, [], $database));
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
