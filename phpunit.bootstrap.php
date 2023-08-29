<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\PdoMysql\PdoMysqlPackage;
use Medas\PdoStorage\Database;
use Medas\RamseyUuidBridge\RamseyUuidBridgePackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};
use Medas\StorageManager\StorageManager;

chdir(__DIR__);

require 'vendor/autoload.php';

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        PdoMysqlPackage::instance(),
        ConfigManagerPackage::instance(),
        ConsolePrinterPackage::instance(),
        RamseyUuidBridgePackage::instance(),
    ]);

    return $config;
});

service(ConfigManager::class)
    ->addDirectory(__DIR__ . '/tests/MockUps')
    ->readEnv(__DIR__);

$database = medas()->objectInstantiator()->instantiate(Database::class);

service(StorageManager::class)
    ->add($database);
