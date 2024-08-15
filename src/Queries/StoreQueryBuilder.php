<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\Definition;
use Medas\PdoStorage\{Database, PdoStorageController, Queries\ParameterizedQuery};

#[Service]
readonly class StoreQueryBuilder
{
    public function __construct(
        public PdoStorageController                     $pdoStorageController,
        public SelectorQueryBuilder\ConditionsProcessor $conditionsProcessor,
        public SelectorQueryBuilder\PaginationProcessor $paginationProcessor,
        public SelectorQueryBuilder\ParametersProcessor $parametersProcessor,
        public SelectorQueryBuilder\RelationsProcessor  $relationsProcessor,
        public SelectorQueryBuilder\SortingProcessor    $sortingProcessor,
    )
    {
    }

    public function buildQuery(
        Database   $database,
        Definition $definition,
        string     $storeName,
        string     $entityName = ''
    ): ParameterizedQuery
    {
        $job = new SelectorQueryBuilder\Job(
            $database,
            $this->pdoStorageController->getDatabaseController($database)->driverHandler,
            $entityName,
        );

        $quotedMainStore = $job->driverHandler->quote($database, $storeName);
        $job->stores = [$job->mainEntity => $quotedMainStore];
        $job->query = 'select * from ' . $quotedMainStore;

        $this->relationsProcessor->process($job, $definition->relations);
        $this->conditionsProcessor->process($job, $definition->conditions);
        $this->sortingProcessor->process($job, $definition->sorts);
        $this->parametersProcessor->process($job, $definition->parameters);
        $this->paginationProcessor->process($job, $definition->pagination);

        return new ParameterizedQuery(
            $job->query,
            $definition->parameters,
            $job->foundConstants,
            $database
        );
    }
}
