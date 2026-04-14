<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Queries\Builders as PdoStorageBuilders;
use Medas\StorageManager\Interfaces\{Fetchers, RecordFetchers as RecordFetchersInterface};

#[Service]
readonly class RecordFetchers implements RecordFetchersInterface
{
    public function __construct(
        private PdoStorageBuilders\CollectionRecordFetcher $collectionRecordFetcher,
        private PdoStorageBuilders\FilteredFetcher         $filteredFetcher,
    )
    {
    }

    public function filteredFetcher(): Fetchers\FilteredFetcher
    {
        return $this->filteredFetcher;
    }

    public function collectionRecordFetcher(): Fetchers\CollectionRecordFetcher
    {
        return $this->collectionRecordFetcher;
    }
}
