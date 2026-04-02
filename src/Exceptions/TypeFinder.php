<?php

declare(strict_types=1);

namespace Medas\PdoMysql\Exceptions;

use Medas\Core\{Attributes\Service, Exceptions\StorageExceptionType};
use Medas\PdoStorage\Drivers\Interfaces\ExceptionTypeFinder;

#[Service]
readonly class TypeFinder implements ExceptionTypeFinder
{
    public function find(\Exception|\Error $e): StorageExceptionType
    {
        // PDOException carries driver-specific error info in errorInfo[1] (MySQL errno)
        $errno = $e instanceof \PDOException ? ($e->errorInfo[1] ?? null) : null;

        return match ($errno) {
            1062 => StorageExceptionType::DuplicateKey,
            1216, 1217, 1452 => StorageExceptionType::ForeignKeyViolation,
            1213 => StorageExceptionType::DeadlockDetected,
            1205 => StorageExceptionType::LockWaitTimeout,
            2006, 2013 => StorageExceptionType::ConnectionLost,
            default => StorageExceptionType::Unknown,
        };
    }
}
