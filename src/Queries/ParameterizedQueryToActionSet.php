<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Queries\{ParameterizedQuery, Query, QuerySet};

#[Service]
readonly class ParameterizedQueryToActionSet
{
    public function compile(ParameterizedQuery $paraQuery, array $arguments = []): QuerySet
    {
        $query = new Query($paraQuery->query, $paraQuery->constants, $paraQuery->database);

        foreach ($paraQuery->parameters as $parameter) {
            if (array_key_exists($parameter->name, $arguments)) {
                $value = $arguments[$parameter->name];
            }
            elseif ($parameter->hasDefault) {
                $value = $parameter->default;
            }
            else {
                throw new \Exception('no value given for parameter ' . $parameter->name);
            }

            $query->arguments[$parameter->name] = $value;
        }

        return QuerySet::fromQuery($query);
    }
}
