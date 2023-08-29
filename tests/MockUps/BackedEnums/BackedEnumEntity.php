<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\BackedEnums;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity(store: 'backed_enum_entities')]
class BackedEnumEntity
{
    #[Id]
    private IntBackedEnum $enum;

    private StringBackedEnum $stringBackedEnum;
}
