<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Queries\SelectorQueryBuilder;

use Medas\Core\Attributes\Service;
use Medas\EntityManager\Selector\{
    Conditions\Condition,
    Conditions\WhereContains,
    Conditions\WhereEndsWith,
    Conditions\WhereIn,
    Conditions\WhereIs,
    Conditions\WhereIsAtLeast,
    Conditions\WhereIsAtMost,
    Conditions\WhereIsLessThan,
    Conditions\WhereIsMoreThan,
    Conditions\WhereIsNot,
    Conditions\WhereIsNotNull,
    Conditions\WhereIsNull,
    Conditions\WhereNotIn,
    Conditions\WhereStartsWith,
    Exceptions\UnhandledConditionType,
    Exceptions\UnhandledOperantType,
    Operants\Argument,
    Operants\ArgumentArray,
    Operants\Operant,
    Operants\Property,
    Operants\Value,
    Operants\Values
};
use Medas\StorageManager\Shared\ValueSerializer;

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
                WhereIsNot::class => $this->processComparison($job, $condition, '!='),
                WhereIn::class => $this->processComparison($job, $condition, ' in '),
                WhereNotIn::class => $this->processComparison($job, $condition, ' not in '),
                WhereIsMoreThan::class => $this->processComparison($job, $condition, '>'),
                WhereIsLessThan::class => $this->processComparison($job, $condition, '<'),
                WhereIsAtLeast::class => $this->processComparison($job, $condition, '>='),
                WhereIsAtMost::class => $this->processComparison($job, $condition, '<='),
                WhereIsNull::class => $this->processNullComparison($job, $condition, true),
                WhereIsNotNull::class => $this->processNullComparison($job, $condition, false),
                WhereContains::class => $this->processLikeComparison($job, $condition, '%', '%'),
                WhereStartsWith::class => $this->processLikeComparison($job, $condition, '', '%'),
                WhereEndsWith::class => $this->processLikeComparison($job, $condition, '%', ''),
                default => throw new UnhandledConditionType($condition),
            };

            $isFirstCondition = false;
        }
    }

    private function processNullComparison(Job $job, WhereIsNull $condition, bool $isNull): void
    {
        $job->query .= $this->operantToQuery($job, $condition->property)
            . ($isNull ? ' is null' : ' is not null');
    }

    private function processLikeComparison(Job $job, WhereIs $condition, string $prefix, string $suffix): void
    {
        if ($condition->value instanceof Value) {
            $condition->value->value = $prefix . $condition->value->value . $suffix;
        }

        $this->processComparison($job, $condition, ' like ');
    }

    private function processComparison(Job $job, WhereIs $condition, string $operator): void
    {
        $nameQuery = $this->operantToQuery($job, $condition->property);
        $addOrIsNull = false;
        $valueQuery = $this->operantToQuery($job, $condition->value, $addOrIsNull);
        $baseQuery = $nameQuery . $operator . $valueQuery;

        if ($addOrIsNull) {
            $job->query .= '(' . $baseQuery . ' or ' . $nameQuery . ' is null)';
        }
        else {
            $job->query .= $baseQuery;
        }
    }

    private function operantToQuery(Job $job, Operant $operant, bool &$addOrIsNull = null): string
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

        if ($operant instanceof ArgumentArray) {
            $job->foundArguments[$operant->name] = true;
            $job->variableSizedParameters[$operant->name] = true;

            return '(:' . $operant->name . ')';
        }

        if ($operant instanceof Value) {
            return $this->addValue($operant->value, $job);
        }

        if ($operant instanceof Values) {
            $names = [];

            foreach ($operant->value as $value) {
                if ($value === null) {
                    $addOrIsNull = true;
                }
                else {
                    $names[] = $this->addValue($value, $job);
                }
            }

            return '(' . implode(',', $names) . ')';
        }

        throw new UnhandledOperantType($operant);
    }

    private function addValue(mixed &$value, Job $job): string
    {
        $value = service(ValueSerializer::class)->serialize($value);
        $name = 'c' . count($job->foundConstants);
        $job->foundConstants[$name] = $value;

        return ':' . $name;
    }
}
