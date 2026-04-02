<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\{Attributes\Service, Interfaces\Serializer};
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\{
    DriverHandler,
    Interfaces\ExceptionTypeFinder,
    Interfaces\FieldHandler,
    Interfaces\QueryBuilders as QueryBuildersInterface,
    Interfaces\TableStructureFinder as TableStructureFinderInterface,
    Interfaces\TypeHandler as TypeHandlerInterface
};
use Medas\PdoStorage\Table;
use Medas\StorageManager\{
    Interfaces\RecordFetchers as RecordFetchersInterface,
    Shared\ValueSerializer
};

#[Service]
readonly class MysqlHandler implements DriverHandler
{
    private TableCollection $tableCollection;

    public function __construct(
        private DataControl\Escaper                                       $escaper,
        private Exceptions\TypeFinder                                     $typeFinder,
        private Queries\QueryBuilders                                     $queryBuilders,
        private Queries\RecordFetchers                                    $recordFetchers,
        private Structure\FieldToDefinitionConverter                      $fieldToDefinitionConverter,
        private Structure\MigrationBuilder                                $migrationBuilder,
        private Structure\TableStructureFinder                            $tableStructureFinder,
        private Structure\TableStructureFinder\TableStructureStringFinder $tableStructureStringFinder,
        private Types\TypeHandler                                         $typeHandler,
        private ValueSerializer                                           $valueSerializer,
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
        return '`' . str_replace('`', '``', $identifier) . '`';
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
        return $this->tableStructureFinder;
    }

    public function fieldHandler(): FieldHandler
    {
        return $this->fieldToDefinitionConverter;
    }

    public function migrationBuilder(): Structure\MigrationBuilder
    {
        return $this->migrationBuilder;
    }

    public function queryBuilders(): QueryBuildersInterface
    {
        return $this->queryBuilders;
    }

    public function serializer(): Serializer
    {
        return $this->valueSerializer;
    }

    public function typeHandler(): TypeHandlerInterface
    {
        return $this->typeHandler;
    }

    public function recordFetchers(): RecordFetchersInterface
    {
        return $this->recordFetchers;
    }

    public function tableStructureString(Table $table): string|null
    {
        return $this->tableStructureStringFinder->find($table);
    }

    public function exceptionTypeFinder(): ExceptionTypeFinder
    {
        return $this->typeFinder;
    }
}
