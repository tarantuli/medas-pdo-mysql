<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\{AsSingleton, BasePackage};
use Medas\PdoStorage\PdoStoragePackage;

class PdoMysqlPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            PdoStoragePackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
