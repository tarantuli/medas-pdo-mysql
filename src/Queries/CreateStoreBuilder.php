<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\PdoMysql\Structure\CreateTableBuilder;
use Medas\PdoStorage\Queries\QuerySet;
use Medas\StorageManager\Interfaces\{
    Builders\CreateStoreBuilder as CreateStoreBuilderInterface,
    Storage
};
use Medas\StorageManager\Structure\Blueprint;

#[Service]
readonly class CreateStoreBuilder implements CreateStoreBuilderInterface
{
    public function __construct(
        private CreateTableBuilder $createTableBuilder,
    )
    {
    }

    public function build(Storage $storage, Blueprint $blueprint): QuerySet
    {
        return $this->createTableBuilder->create($storage, $blueprint);
    }
}
