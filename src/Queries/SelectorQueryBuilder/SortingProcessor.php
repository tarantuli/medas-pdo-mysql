<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\SelectorQueryBuilder;

use Medas\Core\Attributes\Service;

use Medas\EntityManager\Selector\{
    Exceptions\UnhandledSortType,
    Operants\Property,
    Sorting\SortBy,
    Sorting\SortDirection

};

#[Service]
readonly class SortingProcessor
{
    public const SORTING_DIRECTIONS = [
        SortDirection::ASC->name => 'asc',
        SortDirection::DESC->name => 'desc',
    ];

    /** @param SortBy[] $sorts */
    public function process(Job $job, array $sorts): void
    {
        $parts = [];

        foreach ($sorts as $sort) {
            if ($sort instanceof SortBy && $sort->operant instanceof Property) {
                $parts[] = $job->driverHandler->quote(
                    $job->database,
                    $sort->operant->name
                ) . ' ' . self::SORTING_DIRECTIONS[$sort->direction->name];

                continue;
            }

            throw new UnhandledSortType($sort);
        }

        if ($parts) {
            $job->query .= ' order by ' . implode(', ', $parts);
        }
    }
}
