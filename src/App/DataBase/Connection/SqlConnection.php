<?php

namespace RB\System\App\DataBase\Connection;

class SqlConnection extends SqLiteConnection implements ConnectionInterface
{
    protected $pgConn;

    protected function init(): void
    {
        $config = $this->config->getValue('database.connections.'.self::SQLITE_CONNECTION);
        $this->pgConn = pg_connect(sprintf(
            'host=%s port=%s dbname=%s user=%s password=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['username'],
            $config['password']
        ));
    }

    //TODO
}