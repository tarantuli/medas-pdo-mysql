<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritance;

use Medas\EntityManager\Attributes\{Entity, Id, IsGeneratedValue, IsUnique};

#[Entity('i_stored')]
class Stored
{
    #[Id, IsGeneratedValue]
    public int $id;

    #[IsUnique]
    public string $name;
}
