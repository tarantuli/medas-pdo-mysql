<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\SelectorQueryBuilder;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\{
    Conditions\Condition,
    Conditions\WhereIs,
    Conditions\WhereIsAtLeast,
    Conditions\WhereIsAtMost,
    Conditions\WhereIsLessThan,
    Conditions\WhereIsMoreThan,
    Conditions\WhereIsNotNull,
    Conditions\WhereIsNull,
    Exceptions\UnhandledConditionType,
    Exceptions\UnhandledOperantType,
    Operants\Argument,
    Operants\Operant,
    Operants\Property,
    Operants\Value
};
use Medas\PdoStorage\ValueSerializer;

#[Service]
readonly class ConditionsProcessor
{
    /** @param Condition[] $conditions */
    public function process(Job $job, array $conditions): void
    {
        if ($conditions) {
            $job->query .= ' where ';
        }

        $isFirstCondition = true;

        foreach ($conditions as $condition) {
            if (!$isFirstCondition) {
                $job->query .= ' and ';
            }

            match ($condition::class) {
                WhereIs::class => $this->processComparison($job, $condition, '='),
                WhereIsMoreThan::class => $this->processComparison($job, $condition, '>'),
                WhereIsLessThan::class => $this->processComparison($job, $condition, '<'),
                WhereIsAtLeast::class => $this->processComparison($job, $condition, '>='),
                WhereIsAtMost::class => $this->processComparison($job, $condition, '<='),
                WhereIsNull::class => $this->processNullComparison($job, $condition, true),
                WhereIsNotNull::class => $this->processNullComparison($job, $condition, false),
                default => throw new UnhandledConditionType($condition),
            };

            $isFirstCondition = false;
        }
    }

    private function processComparison(Job $job, WhereIs $condition, string $operator): void
    {
        $job->query .= $this->operantToQuery($job, $condition->property)
            . $operator
            . $this->operantToQuery($job, $condition->value);
    }

    private function operantToQuery(Job $job, Operant $operant): string
    {
        if ($operant instanceof Property) {
            return $job->stores[$operant->entity ?? $job->mainEntity]
                . '.'
                . $job->driverHandler->quote($job->database, $operant->name);
        }

        if ($operant instanceof Argument) {
            $job->foundArguments[$operant->name] = true;

            return ':' . $operant->name;
        }

        if ($operant instanceof Value) {
            $operant->value = service(ValueSerializer::class)->serialize($operant->value);
            $name = sha1(serialize($operant->value));
            $job->foundConstants[$name] = $operant->value;

            return ':' . $name;
        }

        throw new UnhandledOperantType($operant);
    }

    private function processNullComparison(Job $job, WhereIsNull $condition, bool $isNull): void
    {
        $job->query .= $this->operantToQuery($job, $condition->property)
            . ($isNull ? ' is null' : ' is not null');
    }
}
