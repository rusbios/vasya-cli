<?php

use RB\System\Service\TelegramBots\Command\{RunLocalCliCommand, SendMailCommand};

return [
    'token' => $_ENV['TELEGRAM_TOKEN'] ?? null,
    'commands' => [
        'send_mail' => [
            'class' => SendMailCommand::class,
            'description' => 'Отправить email',
        ],
        'run_cli' => [
            'class' => RunLocalCliCommand::class,
            'description' => 'Выпольнить команду на сервере',
        ],
    ],
];