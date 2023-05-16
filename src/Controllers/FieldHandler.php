<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Controllers;

use Medas\PdoStorage\Drivers\Bases\BaseFieldHandler;
use Medas\StorageManager\Structure\Blueprint\{Field};

class FieldHandler extends BaseFieldHandler
{
    public function buildDefinition(Field $field): string|null
    {
        if ($field->isCreationTimestamp || $field->isModificationTimestamp) {
            $default = ' default current_timestamp()';

            if ($field->isModificationTimestamp) {
                $default .= ' on update current_timestamp()';
            }
        }
        elseif ($field->hasDefault) {
            $serializedDefault = $this->driver->serializer()->serialize($field->default);
            $default = ' default ' . $this->driver->escape($serializedDefault);
        }
        else {
            $default = '';
        }

        $baseDefinition = $this->driver->typeHandler()->getBaseDefinition($field);

        if ($baseDefinition === null) {
            return null;
        }

        return $baseDefinition
            . ($field->isNullable ? '' : ' not null')
            . ($field->isGenerated ? ' auto_increment' : '')
            . $default;
    }
}
