<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\{Attributes\Service, Interfaces\Serializer};
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\{DriverHandler,
    Interfaces\ExceptionTypeFinder,
    Interfaces\QueryBuilders as QueryBuildersInterface,};
use Medas\PdoStorage\Queries\Builders\RecordFetchers;
use Medas\PdoStorage\Table;
use Medas\StorageManager\Interfaces\RecordFetchers as RecordFetchersInterface;
use Medas\StorageManager\Shared\ValueSerializer;

#[Service]
readonly class MysqlHandler implements DriverHandler
{
    private TableCollection $tableCollection;

    public function __construct(
        private ValueEscaper                        $valueEscaper,
        private \Medas\PdoMysql\ExceptionTypeFinder $exceptionTypeFinder,
        private Queries\QueryBuilders               $queryBuilders,
        private RecordFetchers                      $recordFetchers,
        private ValueSerializer                     $valueSerializer,
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
        return $this->valueEscaper->escape($database, $value);
    }

    public function table(Database $database, string $name): Table
    {
        return $this->tableCollection->get($database, $name);
    }

    public function queryBuilders(): QueryBuildersInterface
    {
        return $this->queryBuilders;
    }

    public function serializer(): Serializer
    {
        return $this->valueSerializer;
    }

    public function recordFetchers(): RecordFetchersInterface
    {
        return $this->recordFetchers;
    }

    public function exceptionTypeFinder(): ExceptionTypeFinder
    {
        return $this->exceptionTypeFinder;
    }
}
