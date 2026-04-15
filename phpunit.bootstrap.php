<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\Events\EventsPackage;
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\PdoMysql\PdoMysqlPackage;
use Medas\PdoStorage\Database;
use Medas\RamseyUuidBridge\RamseyUuidBridgePackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};
use Medas\StorageManager\StorageManager;
use Medas\StorageManagerTests\StorageManagerTestsPackage;

chdir(__DIR__);

require 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig(ObjectInstantiator::class);

    $config->addPackages([
        ConfigManagerPackage::instance(),
        ConfigOptionsPackage::instance(),
        EventsPackage::instance(),
        PdoMysqlPackage::instance(),
        RamseyUuidBridgePackage::instance(),
        StorageManagerTestsPackage::instance(),
    ]);

    return $config;
});

service(ConfigManager::class)
    ->addDirectory(__DIR__ . '/vendor/morphp/medas-storage-manager-tests/src')
    ->readEnv(__DIR__);

$database = medas()->objectInstantiator()->instantiate(Database::class);

service(StorageManager::class)
    ->add($database);
