<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional\ConsoleCommands;

use Medas\ConfigManager\ConfigManager;
use Medas\ConfigOptions\OptionController;
use Medas\PdoMysqlTest\Functional\BaseTestClass;
use Medas\StorageManager\ConfigOptions\EntityDirectory;
use Medas\StorageManager\ConfigOptions\MigrationDirectory;
use Medas\StorageManager\ConsoleCommands\MakeMigrationCommand;
use Medas\StorageManager\ConsoleCommands\MigrateCommand;
use Medas\StorageManager\Migrations\MigrationManager;

class ConsoleCommandsTest extends BaseTestClass
{
    public function testMakeMigrationCommand(): void
    {
        $this->controller()->deleteStore($this->store('new_stored_entities'));
        $directory = $this->getDirectory();
        $initialCount = count(glob($directory . '/*'));

        $this->makeMigration();

        self::assertCount($initialCount + 1, glob($directory . '/*'));

        $this->cleanUp();
    }

    private function getDirectory(): string
    {
        return service(OptionController::class)->getValue(service(MigrationDirectory::class));
    }

    private function makeMigration(): void
    {
        $this->setMigrationEntityDirectory();

        ob_start();
        service(MakeMigrationCommand::class)->process([]);
        ob_end_clean();
    }

    private function setMigrationEntityDirectory(): void
    {
        service(ConfigManager::class)->setValue(
            service(OptionController::class)->getPath(service(EntityDirectory::class)),
            'tests/MockUps/Migrations'
        );
    }

    private function cleanUp(): void
    {
        foreach (glob($this->getDirectory() . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testMigrateCommand(): void
    {
        $this->makeMigration();

        service(MigrateCommand::class)->process([]);

        self::assertCount(1, service(MigrationManager::class)->processedMigrations());
        $this->cleanUp();
    }
}
