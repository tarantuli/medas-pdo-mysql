<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Controllers;

use Medas\PdoStorage\Drivers\Bases\BaseAlterTableBuilder;
use Medas\PdoStorage\Drivers\Bases\BuildJob;

class AlterTableBuilder extends BaseAlterTableBuilder
{
    private ForeignKeyConstraintBuilder $foreignKeyConstraintBuilder;

    public function foreignKeyConstraintBuilder(BuildJob $job): ForeignKeyConstraintBuilder
    {
        return $this->foreignKeyConstraintBuilder;
    }

    protected function initialize(): void
    {
        $this->foreignKeyConstraintBuilder = new ForeignKeyConstraintBuilder();
    }
}
