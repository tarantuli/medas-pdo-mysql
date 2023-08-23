<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Types;

use Medas\Core\Attributes\Service;

#[Service]
readonly class FloatHandler
{
    public function handle(): string
    {
        return 'float';
    }
}
