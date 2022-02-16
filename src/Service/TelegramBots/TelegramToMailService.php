<?php

namespace RB\System\Service\TelegramBots;

use RB\System\App\Config;
use RB\System\Service\Http\HttpService;
use RB\System\Service\TelegramService;

class TelegramToMailService extends TelegramService
{
    public function __construct(Config $config, HttpService $httpService)
    {
        $token = $config->getValue('telegram.bots.mail.token');
        parent::__construct($token, $httpService);
    }
}
