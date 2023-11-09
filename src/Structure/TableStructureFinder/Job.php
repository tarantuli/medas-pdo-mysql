<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Structure\TableStructureFinder;

use Medas\PdoStorage\{Database, Table};
use Medas\StorageManager\Structure\Blueprint;

class Job
{
    public Blueprint $blueprint;
    public string|null $createTable;

    public function __construct(
        public readonly Database $database,
        public readonly Table    $table,
    )
    {
        $this->blueprint = new Blueprint();
    }
}
