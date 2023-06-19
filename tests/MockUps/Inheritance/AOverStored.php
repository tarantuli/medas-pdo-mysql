<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_a_stored')]
class AOverStored extends Stored
{
    public string $aProperty;
}
