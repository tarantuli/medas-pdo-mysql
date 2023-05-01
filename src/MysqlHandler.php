<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\GlobalRepository;
use Medas\PdoStorage\Drivers\BaseHandler;

class MysqlHandler extends BaseHandler
{
    public function quote(string $identifier): string
    {
        return '`' . $identifier . '`';
    }

    public function escape(mixed $value): string
    {
        return $this->controller->escapeValue($value);
    }

    protected function initialize(): void
    {
        $givenArguments = [
            'driver' => $this,
            'controller' => $this->controller,
            'database' => $this->controller->database(),
        ];

        $oi = GlobalRepository::objectInstantiator();

        $this->tableStructureFinder = $oi->instantiate(Controllers\TableStructureFinder::class, $givenArguments);
        $this->alterTableBuilder = $oi->instantiate(Controllers\AlterTableBuilder::class, $givenArguments);
        $this->createTableBuilder = $oi->instantiate(Controllers\CreateTableBuilder::class, $givenArguments);
        $this->fieldHandler = $oi->instantiate(Controllers\FieldHandler::class, $givenArguments);
        $this->migrationBuilder = $oi->instantiate(Controllers\MigrationBuilder::class, $givenArguments);
        $this->queryBuilder = $oi->instantiate(Controllers\QueryBuilder::class, $givenArguments);
        $this->selectQueryBuilder = $oi->instantiate(Controllers\SelectQueryBuilder::class, $givenArguments);
        $this->serializer = $oi->instantiate(Controllers\Serializer::class, $givenArguments);
        $this->typeHandler = $oi->instantiate(Controllers\TypeHandler::class, $givenArguments);
    }
}
