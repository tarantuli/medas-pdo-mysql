<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\MockUps\BackedEnums;

enum IntBackedEnum: int
{
    case Value1 = 1;
    case Value2 = 2;
}
