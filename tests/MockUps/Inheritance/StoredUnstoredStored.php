<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_stored_unstored_stored')]
class StoredUnstoredStored extends UnstoredStored
{
    public string $storedUnstoredStored;
}
