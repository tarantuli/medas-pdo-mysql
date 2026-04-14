<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Exceptions;

use Medas\PdoStorage\Exceptions\UntrustedValueUsedInSorting as ExcepUntrustedValueUsedInSorting;

#[\Deprecated("use the class from pdo-storage instead")]
class UntrustedValueUsedInSorting extends ExcepUntrustedValueUsedInSorting
{
}
