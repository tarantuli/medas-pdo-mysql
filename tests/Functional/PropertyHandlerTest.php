<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional;

use Medas\PdoStorage\Table;


class PropertyHandlerTest extends BaseTestClass
{
    public function testCreateStorage(): void
    {
        $this->controller()->deleteStore($this->store('entities_with_handler'));

        $migration = $this->createMigrationClassContent('PropertyHandlers');

        self::assertStringContainsString('`propertyClass` text not null', $migration);

        $this->executeMigration($migration);

        self::assertInstanceOf(Table::class, $this->store('entities_with_handler'));
    }
}
