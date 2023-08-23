<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Types;

use Medas\Core\Attributes\Service;

#[Service]
readonly class BooleanHandler
{
    public function handle(): string
    {
        return 'tinyint unsigned';
    }
}
