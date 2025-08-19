<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\StoreQueryBuilder;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\Pagination;

#[Service]
readonly class PaginationProcessor
{
    public function process(Job $job, Pagination|null $pagination): void
    {
        if ($pagination === null) {
            return;
        }

        $limit = $pagination->perPage;
        $offset = ($pagination->page - 1) * $pagination->perPage;
        $job->query .= ' limit ' . $offset . ', ' . $limit;
    }
}
