<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\{Database, Events\DatabaseControllerRequest};
use Medas\StorageManager\{Interfaces\Storage, Sequences\SequenceValueSupplier};

#[Service]
readonly class MysqlSequenceValueSupplier implements SequenceValueSupplier
{
    public function canHandle(Storage $storage): bool
    {
        // A PDO storage speaking the mysql driver (the DSN scheme).
        return $storage instanceof Database && str_starts_with($storage->dsn, 'mysql:');
    }

    public function priority(): int
    {
        return -100;
    }

    public function next(Storage $storage, string $table, string $scope, int $year): int
    {
        /** @var Database $storage */
        $pdo = dispatch(new DatabaseControllerRequest($storage))->databaseController->pdo;
        $quotedTable = '`' . str_replace('`', '``', $table) . '`';

        // One atomic statement: on the first use for a (scope, year) it inserts
        // value 1; afterward it increments. Either branch routes the new value
        // through LAST_INSERT_ID(), and the ON DUPLICATE KEY UPDATE takes a row
        // lock on the (scope, year) unique key - so concurrent callers serialize
        // and each reads back its own value below, never a shared one.
        $statement
            = $pdo->prepare("INSERT INTO $quotedTable (`scope`, `year`, `value`) VALUES (:scope, :year, LAST_INSERT_ID(1)) " . "ON DUPLICATE KEY UPDATE `value` = LAST_INSERT_ID(`value` + 1)");

        $statement->execute(['scope' => $scope, 'year' => $year]);

        return (int) $pdo->lastInsertId();
    }
}
