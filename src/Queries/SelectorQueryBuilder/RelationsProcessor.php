<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\SelectorQueryBuilder;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\Exceptions\UnhandledRelationType;
use Medas\EntityManager\Selector\Relations\Relation;

#[Service]
readonly class RelationsProcessor
{
    /**
     * @param Relation[] $relations
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function process(Job $job, array $relations): void
    {
        foreach ($relations as $relation) {
            throw new UnhandledRelationType($relation);
        }
    }
}
