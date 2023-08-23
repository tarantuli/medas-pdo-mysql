<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\SelectorQueryBuilder;

use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\DriverHandler;

class Job
{
    public string $query;
    public array $stores;
    public array $foundArguments = [];
    public array $foundConstants = [];

    public function __construct(
        public Database        $database,
        public DriverHandler   $driverHandler,
        public readonly string $mainEntity,
    )
    {
    }
}
