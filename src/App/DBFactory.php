<?php

namespace RB\System\App;

use RB\System\App\DataBase\Connection\{ConnectionInterface, SqlConnection, SqLiteConnection};
use RB\System\Exception\DataBaseException;

class DBFactory
{
    private static ?self $obj = null;

    private Config $config;

    /** @var ConnectionInterface[] $connections */
    private array $connections = [];

    private function __construct(Config $config)
    {
        $this->config = $config;
    }

    public static function create(Config $config): self
    {
        if (!self::$obj) {
            self::$obj = new self($config);
        }

        return self::$obj;
    }

    /**
     * @param string|null $name
     * @return ConnectionInterface
     * @throws DataBaseException
     */
    public function getConnection(?string $name = null): ConnectionInterface
    {
        $name = $name ?: $this->config->getValue('database.default');

        if (empty($this->connections[$name])) {
            switch ($name) {
                case ConnectionInterface::SQLITE_CONNECTION:
                    $this->connections[$name] = new SqLiteConnection($this->config);
                    break;

                case ConnectionInterface::SQL_CONNECTION:
                    $this->connections[$name] = new SqlConnection($this->config);
                    break;
                default:
                    throw new DataBaseException('Connection not found');
            }
        }

        return $this->connections[$name];
    }
}