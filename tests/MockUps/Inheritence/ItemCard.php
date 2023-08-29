<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\Inheritence;

use Medas\EntityManager\Attributes\Entity;

#[Entity]
class ItemCard extends Card
{
    public string $itemType;
}
