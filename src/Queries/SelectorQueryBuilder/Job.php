<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\SelectorQueryBuilder;

use Medas\PdoStorage\{Database, Drivers\DriverHandler};

class Job
{
    public string $query;
    public array $stores;
    public array $foundArguments = [];
    public array $foundConstants = [];
    public array $variableSizedParameters = [];

    public function __construct(
        public Database        $database,
        public DriverHandler   $driverHandler,
        public readonly string $mainEntity,
    )
    {
    }
}
