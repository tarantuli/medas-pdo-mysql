<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional;

use Medas\PdoStorage\PdoStorageController;
use Medas\StorageManager\Interfaces\{Storage, StorageController, Store};
use Medas\StorageManager\Migrations\MigrationBuildManager;
use Medas\StorageManager\Migrations\MigrationManager;
use Medas\StorageManager\StorageManager;
use PHPUnit\Framework\TestCase;

abstract class BaseTestClass extends TestCase
{
    private Storage $storage;
    private StorageController $controller;

    protected function initialize(): void
    {
    }

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
            $this->controller = service(PdoStorageController::class);
        }

        return $this->controller;
    }

    protected function createMigrationClassContent(string $directory): string|null
    {
        $buildManager = service(MigrationBuildManager::class);
        $realDirectory = realpath(__DIR__ . '/../MockUps/' . $directory);

        if ($realDirectory === false) {
            throw new \Exception('directory "' . __DIR__ . '/../MockUps/' . $directory . '" does not exist');
        }

        return $buildManager->createMigrationClass($realDirectory);
    }

    protected function executeMigration(string $migration): void
    {
        preg_match('/class (Migration\d+)/', $migration, $match);
        $directory = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';
        $fileName = $directory . DIRECTORY_SEPARATOR . $match[1] . '.php';

        // Prepare the migration test directory
        if (!file_exists($directory)) {
            mkdir($directory);
        }
        else {
            foreach (glob($directory . DIRECTORY_SEPARATOR . '*') as $existingFile) {
                unlink($existingFile);
            }
        }

        // Execute the migration
        file_put_contents($fileName, $migration);
        $manager = service(MigrationManager::class);
        $manager->migrate($directory);

        // Remove the test directory
        unlink($fileName);
        rmdir($directory);
    }
}
