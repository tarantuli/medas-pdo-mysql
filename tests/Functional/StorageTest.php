<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional;

use Medas\PdoStorage\PdoStorageController;
use Medas\StorageManager\Interfaces\Storage;
use Medas\StorageManager\Interfaces\StorageController;
use Medas\StorageManager\StorageManager;
use Medas\StorageManagerTest\Functional\StorageTests\AbstractStorageTestClass;

class StorageTest extends AbstractStorageTestClass
{
    protected function initialize(): void
    {
    }

    protected function storage(): Storage
    {
        return service(StorageManager::class)->byName('default');
    }

    protected function controller(): StorageController
    {
        return service(PdoStorageController::class);
    }

    protected function migrationAssertions(string $migration): void
    {
        self::assertStringContainsString('alter table', $migration);
    }
}
