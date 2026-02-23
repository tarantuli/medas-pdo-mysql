<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Types;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\Interfaces\TypeHandler as TypeHandlerInterface;
use Medas\StorageManager\Structure\Blueprint\{Field, Type};

#[Service]
readonly class TypeHandler implements TypeHandlerInterface
{
    public function __construct(
        private BinaryHandler     $binaryHandler,
        private BooleanHandler    $booleanHandler,
        private CollectionHandler $collectionHandler,
        private DateHandler       $dateHandler,
        private DateTimeHandler   $dateTimeHandler,
        private FloatHandler      $floatHandler,
        private IntegerHandler    $integerHandler,
        private TextHandler       $textHandler,
    )
    {
    }

    public function getBaseDefinition(Database $database, Field $field): string|null
    {
        return match ($field->type) {
            Type::Binary => $this->binaryHandler->handle($field),
            Type::Boolean => $this->booleanHandler->handle(),
            Type::Collection => $this->collectionHandler->handle(),
            Type::Date => $this->dateHandler->handle(),
            Type::DateTime => $this->dateTimeHandler->handle(),
            Type::Float => $this->floatHandler->handle(),
            Type::Integer => $this->integerHandler->handle($field),
            Type::Text => $this->textHandler->handle($field),
        };
    }
}
