<?php

return [
    'name' => $_ENV['APP_NAME'] ?? 'Project',
    'env' => $_ENV['APP_ENV'] ?? 'prod',
    'debug' => (bool) ($_ENV['APP_DEBUG'] ?? false),





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