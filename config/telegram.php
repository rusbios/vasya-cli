<?php

return [
    'bots' => [
        'mail' => [
            'token' => $_ENV['TELEGRAM_TOKEN_BOT_MAIL'] ?? null,
        ],
        'command' => [
            'token' => $_ENV['TELEGRAM_TOKEN_COMMAND'] ?? null,
        ],
    ],
];