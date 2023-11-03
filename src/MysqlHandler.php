<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\{Attributes\Service, Interfaces\Serializer};
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\{DriverHandler,
    Interfaces\FieldHandler,
    Interfaces\QueryBuilders as QueryBuildersInterface,
    Interfaces\TableStructureFinder as TableStructureFinderInterface,
    Interfaces\TypeHandler as TypeHandlerInterface};
use Medas\PdoStorage\Table;
use Medas\PdoStorage\ValueSerializer;
use Medas\StorageManager\Interfaces\RecordFetchers as RecordFetchersInterface;

#[Service]
readonly class MysqlHandler implements DriverHandler
{
    private TableCollection $tableCollection;

    public function __construct(
        private DataControl\Escaper $escaper,
        private ValueSerializer     $valueSerializer,
    )
    {
        $this->tableCollection = new TableCollection();
    }

    public function canHandle(string $driverName): bool
    {
        return $driverName === 'mysql';
    }

    public function priority(): int
    {
        return -100;
    }

    public function quote(Database $database, string $identifier): string
    {
        return '`' . $identifier . '`';
    }

    public function escape(Database $database, mixed $value): string
    {
        return $this->escaper->escape($database, $value);
    }

    public function table(Database $database, string $name): Table
    {
        return $this->tableCollection->get($database, $name);
    }

    public function tableStructureFinder(): TableStructureFinderInterface
    {
        // Don't use injection, so it's only initialized when needed
        return service(Structure\TableStructureFinder::class);
    }

    public function fieldHandler(): FieldHandler
    {
        // Don't use injection, so it's only initialized when needed
        return service(Structure\FieldToDefinitionConverter::class);
    }

    public function migrationBuilder(): Structure\MigrationBuilder
    {
        // Don't use injection, so it's only initialized when needed
        return service(Structure\MigrationBuilder::class);
    }

    public function queryBuilders(): QueryBuildersInterface
    {
        // Don't use injection, so it's only initialized when needed
        return service(Queries\QueryBuilders::class);
    }

    public function serializer(): Serializer
    {
        return $this->valueSerializer;
    }

    public function typeHandler(): TypeHandlerInterface
    {
        // Don't use injection, so it's only initialized when needed
        return service(Types\TypeHandler::class);
    }

    public function recordFetchers(): RecordFetchersInterface
    {
        // Don't use injection, so it's only initialized when needed
        return service(Queries\RecordFetchers::class);
    }

    public function tableStructureString(Table $table): string|null
    {
        // Don't use injection, so it's only initialized when needed
        return service(Structure\TableStructureFinder\TableStructureStringFinder::class)->find($table);
    }
}
