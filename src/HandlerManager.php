<?php

declare(strict_types=1);

namespace Medas\PdoMysql;

use Medas\Core\Attributes\Service;
use Medas\PdoStorage\DatabaseController;
use Medas\PdoStorage\Drivers\Handler;
use Medas\PdoStorage\Drivers\HandlerManager as HandlerManagerInterface;

#[Service]
class HandlerManager implements HandlerManagerInterface
{
    public function canHandle(string $driverName): bool
    {
        return $driverName === 'mysql';
    }

    public function priority(): int
    {
        return -100;
    }

    public function initialize(DatabaseController $controller): Handler
    {
        return new MysqlHandler($controller);
    }
}
