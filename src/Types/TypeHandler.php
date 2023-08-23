<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Types;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Database;
use Medas\PdoStorage\Drivers\Interfaces\TypeHandler as TypeHandlerInterface;
use Medas\StorageManager\Structure\Blueprint\Field;
use Medas\StorageManager\Structure\Blueprint\Type;

#[Service]
readonly class TypeHandler implements TypeHandlerInterface
{
    public function __construct(
        private BinaryHandler     $binaryHandler,
        private BooleanHandler    $booleanHandler,
        private CollectionHandler $collectionHandler,
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
            Type::Text => $this->textHandler->handle($field),
            Type::Binary => $this->binaryHandler->handle($field),
            Type::DateTime => $this->dateTimeHandler->handle(),
            Type::Integer => $this->integerHandler->handle($field),
            Type::Boolean => $this->booleanHandler->handle(),
            Type::Float => $this->floatHandler->handle(),
            Type::Collection => $this->collectionHandler->handle(),
        };
    }
}
