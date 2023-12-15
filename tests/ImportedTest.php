<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest;

use Medas\StorageManager\{
    Interfaces\Storage,
    Interfaces\StorageController,
    Interfaces\Store,
    StorageManager
};
use Medas\StorageManagerTests\Functional\AllTests;
use PHPUnit\Framework\TestCase;

class ImportedTest extends TestCase
{
    use AllTests;

    private Storage $storage;
    private StorageController $controller;

    protected function storage(): Storage
    {
        if (!isset($this->storage)) {
            $this->storage = service(StorageManager::class)->byName('default');
        }

        return $this->storage;
    }

    protected function store(string $name): Store
    {
        return $this->controller()->store($name);
    }

    protected function controller(): StorageController
    {
        if (!isset($this->controller)) {
            $this->controller = service(StorageManager::class)->controller($this->storage());
        }

        return $this->controller;
    }

    protected function checkBackedEnumMigration(string $migration): void
    {
        self::assertStringContainsString('`enum` tinyint', $migration);
        self::assertStringContainsString('char(3)', $migration);
    }

    protected function checkPropertyHandlerMigration(string $migration): void
    {
        self::assertStringContainsString('`propertyClass` text not null', $migration);
    }

    protected function preMigrationPreparations(): void
    {
        $this->controller()->deleteStore($this->store('r_groups__labels'));
    }

    protected function migrationAssertions(string $migration): void
    {
        self::assertStringContainsString('alter table', $migration);
    }

    protected function postMigrationAssertions(): void
    {
        self::assertTrue($this->controller()->hasStore($this->store('r_groups__labels')));
    }
}
