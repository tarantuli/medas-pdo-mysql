<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\PdoStorage\Queries\Builders\InsertBuilder as BuildInsertBuilder;

#[\Deprecated("use the class from pdo-storage instead")]
readonly class InsertBuilder extends BuildInsertBuilder
{
}
