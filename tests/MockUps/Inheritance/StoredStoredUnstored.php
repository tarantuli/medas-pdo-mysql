<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_stored_stored_unstored')]
class StoredStoredUnstored extends StoredUnstored
{
    public string $storedStoredUnstored;
}
