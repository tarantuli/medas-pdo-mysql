<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_stored_unstored')]
class StoredUnstored extends Unstored
{
    public string $storedChildUnstoredRoot;
}
