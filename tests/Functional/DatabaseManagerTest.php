<?php

declare(strict_types=1);

namespace Medas\PdoMysqlTest\Functional;

use Medas\PdoStorage\Database;
use Medas\PdoStorage\Table;

class DatabaseManagerTest extends BaseTestClass
{
    public function testConnect(): void
    {
        self::assertInstanceOf(Database::class, $this->storage());
    }

    public function testGetTable(): void
    {
        $table = $this->store('database_manager_test');
        self::assertInstanceOf(Table::class, $table);
    }
}
