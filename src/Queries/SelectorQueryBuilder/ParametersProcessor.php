<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\SelectorQueryBuilder;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\Exceptions\UndeclaredParameters;
use Medas\EntityManager\Selector\Parameter;

#[Service]
readonly class ParametersProcessor
{
    /** @param Parameter[] $parameters */
    public function process(Job $job, array $parameters): void
    {
        foreach ($parameters as $parameter) {
            unset($job->foundArguments[$parameter->name]);
        }

        if ($job->foundArguments) {
            throw new UndeclaredParameters(array_keys($job->foundArguments));
        }
    }
}
