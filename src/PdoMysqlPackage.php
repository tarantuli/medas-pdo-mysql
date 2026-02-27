<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\{AsSingleton, BasePackage, Interfaces\ServiceConfig};
use Medas\PdoStorage\{Drivers\DriverHandlerManager, PdoStoragePackage};

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

    public function initialize(ServiceConfig $config): void
    {
        parent::initialize($config);

        service(DriverHandlerManager::class)->addHandler(service(MysqlHandler::class));
    }
}
