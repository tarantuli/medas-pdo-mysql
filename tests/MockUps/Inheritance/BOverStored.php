<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_b_stored')]
class BOverStored extends Stored
{
    public string $bProperty;
}
