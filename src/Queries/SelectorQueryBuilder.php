<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\{Attributes\Service, Interfaces\CacheManager, Interfaces\NotCacheable};
use Medas\EntityManager\{MetaDataManager, Selector\Selector};
use Medas\PdoStorage\{
    Database,
    Exceptions\StorageIsNotDatabase,
    Queries\ParameterizedQuery,
    Queries\Query,
    Queries\QuerySet
};
use Medas\StorageManager\Interfaces\Builders\SelectorActionBuilder;
use Medas\StorageManager\StorageManager;
use Medas\StorageManager\UnitOfWork\ActionSet;

#[Service]
readonly class SelectorQueryBuilder implements SelectorActionBuilder
{
    public function __construct(
        public CacheManager      $cacheManager,
        public MetaDataManager   $metaDataManager,
        public StorageManager    $storageManager,
        public StoreQueryBuilder $storeQueryBuilder,
    )
    {
    }

    public function build(Selector $selector, array $arguments): ActionSet
    {
        if ($selector instanceof NotCacheable) {
            $paraQuery = $this->buildParameterizedQuery($selector);
        }
        else {
            /** @var ParameterizedQuery $paraQuery */
            $paraQuery = $this->cacheManager->get()->get(
                [static::class, $selector::class],
                fn() => $this->buildParameterizedQuery($selector)
            );
        }

        return $this->compileToQuery($paraQuery, $arguments);
    }

    private function buildParameterizedQuery(Selector $selector): ParameterizedQuery
    {
        $definition = $selector->definition();
        $metaData = $this->metaDataManager->get($selector->entity());
        $database = $this->storageManager->byName($metaData->entity->storage);

        /** @noinspection PhpConditionAlreadyCheckedInspection */
        if (!$database instanceof Database) {
            throw new StorageIsNotDatabase($metaData->entity->storage);
        }

        return $this->storeQueryBuilder->buildQuery(
            $database,
            $definition,
            $metaData->entity->store,
            $metaData->className
        );
    }

    private function compileToQuery(ParameterizedQuery $paraQuery, array $arguments): QuerySet
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
