<?php

use RB\System\Service\TelegramBots\Command\SendMailCommand;

return [
    'token' => $_ENV['TELEGRAM_TOKEN'] ?? null,
    'commands' => [
        'send_mail' => [
            'class' => SendMailCommand::class,
            'description' => 'Отправить email',
        ],
    ],
];