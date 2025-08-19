<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\StoreQueryBuilder;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\OutputValues\{CalculatedValue, OutputValue, PropertyValue};

#[Service]
readonly class OutputValuesProcessor
{
    public function __construct(
        private OutputValueProcessor\CalculatedValueProcessor $calculatedValueProcessor,
    )
    {
    }

    /** @param OutputValue[] $outputValues */
    public function process(Job $job, array $outputValues): void
    {
        foreach ($outputValues as $outputValue) {
            match (true) {
                $outputValue instanceof CalculatedValue => $this->calculatedValueProcessor->process(
                    $job,
                    $outputValue
                ),

                $outputValue instanceof PropertyValue => $job->outputValues[] = $outputValue->name,
                default => throw new \LogicException('Unsupported output value type'),
            };
        }
    }
}
