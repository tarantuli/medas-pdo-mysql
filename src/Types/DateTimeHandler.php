<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Types;

use Medas\Core\Attributes\Service;

#[Service]
readonly class DateTimeHandler
{
    public function handle(): string
    {
        return 'datetime';
    }
}
