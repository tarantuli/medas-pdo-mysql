<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Controllers;

use Medas\PdoStorage\Drivers\Bases\BaseAlterTableBuilder;

class AlterTableBuilder extends BaseAlterTableBuilder
{
    private ForeignKeyConstraintBuilder $foreignKeyConstraintBuilder;

    function foreignKeyConstraintBuilder(): ForeignKeyConstraintBuilder
    {
        return $this->foreignKeyConstraintBuilder;
    }

    protected function initialize(): void
    {
        $this->foreignKeyConstraintBuilder = new ForeignKeyConstraintBuilder();
    }
}
