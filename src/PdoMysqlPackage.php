<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\AsSingleton;
use Medas\PdoStorage\DriverHandlerManager;
use Medas\PdoStorage\PdoStoragePackage;
use Medas\ServiceManager\BasePackage;
use Medas\ServiceManager\ServiceConfig;

class PdoMysqlPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return $this->dependenciesByClass([
            PdoStoragePackage::class,
        ]);
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
