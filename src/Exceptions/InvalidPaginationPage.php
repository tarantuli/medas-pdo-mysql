<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Exceptions;

use Medas\PdoStorage\Exceptions\InvalidPaginationPage as ExcepInvalidPaginationPage;

#[\Deprecated("use the class from pdo-storage instead")]
class InvalidPaginationPage extends ExcepInvalidPaginationPage
{
}
