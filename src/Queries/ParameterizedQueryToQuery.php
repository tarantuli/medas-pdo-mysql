<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\PdoStorage\Queries\Builders\ParameterizedQueryToQuery as BuildParameterizedQueryToQuery;

#[\Deprecated("use the class from pdo-storage instead")]
readonly class ParameterizedQueryToQuery extends BuildParameterizedQueryToQuery
{
}
