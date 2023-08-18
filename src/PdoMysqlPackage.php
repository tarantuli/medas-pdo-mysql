<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\AsSingleton;
use Medas\PdoStorage\{DriverHandlerManager, PdoStoragePackage};
use Medas\ServiceManager\{BasePackage, ServiceConfig};

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
        service(DriverHandlerManager::class)->addManager(service(HandlerManager::class));
    }
}
