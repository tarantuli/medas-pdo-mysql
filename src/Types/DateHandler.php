<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Types;

use Medas\Core\Attributes\Service;

#[Service]
readonly class DateHandler
{
    public function handle(): string
    {
        return 'date';
    }
}
