<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest;

use Medas\StorageManager\Interfaces\{Storage, StorageController, Store};
use Medas\StorageManager\StorageManager;
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
}
