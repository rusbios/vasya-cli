<?php

use RB\System\Service\TelegramBots\Command\SendMailCommand;

return [
    'token' => $_ENV['TELEGRAM_TOKEN_COMMAND'] ?? null,
    'commands' => [
        'start' => [
            'class' => null,
            'description' => 'Нифо',
        ],
        'send_mail' => [
            'class' => SendMailCommand::class,
            'description' => 'Отправить email',
        ],
    ],
];