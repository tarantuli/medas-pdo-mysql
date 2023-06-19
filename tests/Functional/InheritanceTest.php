<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional;

use Medas\PdoMysqlTest\MockUps\Inheritance\AOverStored;
use Medas\PdoMysqlTest\MockUps\Inheritance\BOverStored;
use Medas\PdoStorage\Database;
use Medas\StorageManager\Migrations\MigrationBuildManager;
use Medas\StorageManager\StorageManager;
use Medas\StorageManagerTest\BaseTestClass;

class InheritanceTest extends BaseTestClass
{
    private const TABLES = [
        'i_a_stored',
        'i_b_stored',
        'i_stored',
        'i_stored_stored',
        'i_stored_stored_stored',
        'i_stored_stored_unstored',
        'i_stored_unstored',
        'i_stored_unstored_stored',
        'i_stored_unstored_unstored',
    ];

    public function testMigrate(): void
    {
        service(StorageManager::class)->add(
            medas()->objectInstantiator()->instantiate(Database::class)
        );

        foreach (self::TABLES as $table) {
            storage()->controller()->deleteStore($table);
        }

        $buildManager = service(MigrationBuildManager::class);
        $realDirectory = realpath(__DIR__ . '/../MockUps/Inheritance');

        $migration = $buildManager->createMigrationClass($realDirectory);
        $this->executeMigration($migration);

        foreach (self::TABLES as $table) {
            self::assertTrue(storage()->store($table)->exists());
        }
    }

    /** @depends testMigrate */
    public function testSharedRoot(): void
    {
        $a = em()->create(AOverStored::class, ['name' => 'aProperty', 'aProperty' => 'aProperty']);
        $b = em()->create(BOverStored::class, ['name' => 'bProperty', 'bProperty' => 'bProperty']);

        em()->persist($a, $b);
        em()->flush();
        em()->clear();

        self::assertNotEquals($a->id, $b->id);
    }
}
