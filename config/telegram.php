<?php

use RB\System\Service\TelegramBots\Command\{
    LoginCommand,
    LogoutCommand,
    RegistrationCommand,
    RunLocalCliCommand,
    SendMailCommand
};

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
        'login' => [
            'class' => LoginCommand::class,
            'description' => 'Авторизация в системе',
        ],
        'registration' => [
            'class' => RegistrationCommand::class,
            'description' => 'Зарегистрироватся',
        ],
        'logout' => [
            'class' => LogoutCommand::class,
            'description' => 'Разавторизоватся',
        ],
    ],
];