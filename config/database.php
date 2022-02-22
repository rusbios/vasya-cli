<?php

use RB\System\App\DataBase\Connection\ConnectionInterface;

return [
    'default' => $_ENV['DB_CONNECTION'] ?? ConnectionInterface::SQLITE_CONNECTION,

    'connections' => [
        ConnectionInterface::SQLITE_CONNECTION => BASE_PATH . ($_ENV['DB_DATABASE'] ?? '/storage/database.sqlite'),

        ConnectionInterface::SQL_CONNECTION => [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
            'database' => $_ENV['DB_DATABASE'] ?? 'rb',
            'username' => $_ENV['DB_USERNAME'] ?? 'rb',
            'password' => $_ENV['DB_PASSWORD'] ?? 'rb',
        ],
    ],
];