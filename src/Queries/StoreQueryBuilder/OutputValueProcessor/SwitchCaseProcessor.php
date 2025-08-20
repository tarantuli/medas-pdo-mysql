<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\StoreQueryBuilder\OutputValueProcessor;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\OutputValues\SwitchCase;
use Medas\PdoMysql\Queries\StoreQueryBuilder\{CalculationsProcessor, Job};

#[Service]
readonly class SwitchCaseProcessor
{
    public function __construct(
        private CalculationsProcessor $calculationsProcessor,
    )
    {
    }

    public function process(Job $job, SwitchCase $switchCase): void
    {
        $count = count($switchCase->cases);
        $sets = floor($count / 2);
        $output = 'case ';

        for ($i = 0; $i < $sets; ++$i) {
            if (array_key_exists($i + 1, $switchCase->cases)) {
                // when X then Y
                $this->calculationsProcessor->process($job, $switchCase->cases[$i]);

                $when = $job->currentCalculation;

                $this->calculationsProcessor->process($job, $switchCase->cases[$i + 1]);

                $then = $job->currentCalculation;
                $output .= "when $when then $then";
            }
            else {
                // else Z
                $this->calculationsProcessor->process($job, $switchCase->cases[$i]);

                $else = $job->currentCalculation;
                $output .= "else $else";
            }
        }

        $output .= ' end';
        $job->outputValues[] = $output;
    }
}
