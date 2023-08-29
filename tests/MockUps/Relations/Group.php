<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Relations;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id, IsGeneratedValue, IsUnique};

#[Entity(store: 'r_groups')]
class Group implements HasId
{
    #[Id, IsGeneratedValue]
    private int $id;

    #[IsUnique]
    private string $name;

    private Labels $labels;

    public function id(): int
    {
        return $this->id;
    }
}
