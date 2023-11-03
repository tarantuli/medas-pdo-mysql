<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\StorageManager\Interfaces\{Fetchers, RecordFetchers as RecordFetchersInterface};

#[Service]
readonly class RecordFetchers implements RecordFetchersInterface
{
    public function filteredFetcher(): Fetchers\FilteredFetcher
    {
        // Don't use injection, so it's only initialized when needed
        return service(FilteredFetcher::class);
    }

    public function collectionRecordFetcher(): Fetchers\CollectionRecordFetcher
    {
        // Don't use injection, so it's only initialized when needed
        return service(CollectionRecordFetcher::class);
    }
}
