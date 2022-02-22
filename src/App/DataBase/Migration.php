<?php

namespace RB\System\App\DataBase;

use RB\System\Exception\DataBaseException;
use RB\System\App\DataBase\Connection\ConnectionInterface;

class Migration
{
    private const DIR = BASE_PATH . '/src/App/DataBase/Migrations/';
    private const NAMESPACE = 'RB\System\App\DataBase\Migrations\\';

    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function run(): array
    {
        $res = $doneMigrate = [];
        try {
            $i = $this->connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migration';");
            if (count($i) > 0) {
                $result = $this->connection->query('select * from `migration` where 1=1;');
            }
        } catch (DataBaseException $e) {
            $res[] = 'ERROR: '. $e->getMessage();
            return $res;
        }

        foreach ($result ?? [] as $item) {
            $doneMigrate[] = $item['name'];
        }

        foreach (scandir(self::DIR) as $file) {
            if (preg_match('/^([a-zA-Z]{5,}[0-9]{12}).php$/', $file, $match) > 0) {
                if (!empty($doneMigrate) && in_array($match[1], $doneMigrate)) {
                    continue;
                }

                $class = self::NAMESPACE . $match[1];

                try {
                    (new $class())->up($this->connection);
                } catch (DataBaseException $e) {
                    $res[] = 'ERROR: ' . $e->getMessage();
                    return $res;
                }

                $level = $this->connection->insert('migration', [
                    'name' => $match[1],
                ]);
                $res[] = 'Success migrate "' . $match[1] . '" level up ' . $level;
            }
        }

        return $res;
    }
}