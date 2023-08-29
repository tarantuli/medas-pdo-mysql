<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps;

readonly class MockUpIds
{
    public function __construct(
        public int $weaponId,
        public int $armorId,

    )
    {
    }
}
