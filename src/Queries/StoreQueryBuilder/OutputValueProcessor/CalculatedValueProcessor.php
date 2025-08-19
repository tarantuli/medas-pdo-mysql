<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\StoreQueryBuilder\OutputValueProcessor;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\OutputValues\{CalculatedValue, Count, Sum};
use Medas\PdoMysql\Exceptions\UnsupportedOutputValueType;
use Medas\PdoMysql\Queries\StoreQueryBuilder\{CalculationsProcessor, Job};

#[Service]
readonly class CalculatedValueProcessor
{
    private const TYPE_MAPPING = [
        CalculatedValue::class => '',
        Count::class => 'count',
        Sum::class => 'sum',
    ];

    public function __construct(
        private CalculationsProcessor $calculationsProcessor,
    )
    {
    }

    public function process(Job $job, CalculatedValue $outputValue): void
    {
        if (!array_key_exists($outputValue::class, self::TYPE_MAPPING)) {
            throw new UnsupportedOutputValueType($outputValue);
        }

        $this->calculationsProcessor->process($job, $outputValue->calculations);

        $job->outputValues[] = sprintf(
            "%s(%s)",
            self::TYPE_MAPPING[$outputValue::class],
            $job->currentCalculation
        );
    }
}
