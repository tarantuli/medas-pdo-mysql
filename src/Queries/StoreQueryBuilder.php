<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\PdoStorage\Queries\Builders\StoreQueryBuilder as BuildStoreQueryBuilder;

#[\Deprecated("use the class from pdo-storage instead")]
readonly class StoreQueryBuilder extends BuildStoreQueryBuilder
{
}
