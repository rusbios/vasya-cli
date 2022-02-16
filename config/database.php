<?php

return [
    'default' => $_ENV['DB_CONNECTION'] ?? 'sql',

    'connections' => [
        'sqlite' => [
            'url' => $_ENV['DATABASE_URL'] ?? __DIR__ . '/../../../storage/',
            'database' => $_ENV['DB_DATABASE'] ?? 'database.sqlite',
        ],

        'sql' => [
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
            'database' => $_ENV['DB_DATABASE'] ?? 'rb',
            'username' => $_ENV['DB_USERNAME'] ?? 'rb',
            'password' => $_ENV['DB_PASSWORD'] ?? 'rb',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],

    'redis' => [
        'default' => [
            'host' => $_ENV['REDIS_HOST'] ?? 'localhost',
            'port' => (int) ($_ENV['REDIS_PORT'] ?? 6379),
        ],

        'cache' => [
            'host' => $_ENV['REDIS_CACHE_HOST'] ?? 'localhost',
            'port' => (int) ($_ENV['REDIS_CACHE_PORT'] ?? 6379),
        ],
    ],

    'elasticsearch' => [

    ],

    'rabbitmq' => [
        'host' => $_ENV['RMQ_HOST'] ?? 'localhost',
        'port' => (int) ($_ENV['RMQ_PORT'] ?? 5672),
        'username' => $_ENV['RMQ_USERNAME'] ?? 'rb',
        'password' => $_ENV['RMQ_PASSWORD'] ?? 'rb',
    ],
];