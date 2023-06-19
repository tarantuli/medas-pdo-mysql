<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_stored_stored')]
class StoredStored extends Unstored
{
    public string $storedChildStoredRoot;
}
