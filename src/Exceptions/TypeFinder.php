<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Exceptions;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\Drivers\Interfaces\ExceptionTypeFinder;
use Medas\PdoStorage\Exceptions\ExceptionType;

#[Service]
readonly class TypeFinder implements ExceptionTypeFinder
{
    public function find(\Exception|\Error $e): ExceptionType
    {
        // PDOException carries driver-specific error info in errorInfo[1] (MySQL errno)
        $errno = $e instanceof \PDOException ? ($e->errorInfo[1] ?? null) : null;

        return match ($errno) {
            1062 => ExceptionType::DuplicateKey,
            1216, 1217, 1452 => ExceptionType::ForeignKeyViolation,
            1213 => ExceptionType::DeadlockDetected,
            1205 => ExceptionType::LockWaitTimeout,
            2006, 2013 => ExceptionType::ConnectionLost,
            default => ExceptionType::Unknown,
        };
    }
}
