<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\PdoStorage\Queries\Builders\DropTableBuilder as BuildDropTableBuilder;

#[\Deprecated("use the class from pdo-storage instead")]
readonly class DropTableBuilder extends BuildDropTableBuilder
{
}
