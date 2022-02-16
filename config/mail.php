<?php

return [
    'connect' => [
        'host' => $_ENV['MAIL_HOST'] ?? 'smtp.mailgun.org',
        'port' => (int) ($_ENV['MAIL_PORT'] ?? 587),
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'timeout' => null,
    ],

    'from' => [
        'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@example.com',
        'name' => $_ENV['MAIL_FROM_NAME'] ?? 'Example',
    ],
];