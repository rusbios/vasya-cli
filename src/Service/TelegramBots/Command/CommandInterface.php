<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\Config;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

interface CommandInterface
{
    const ACTIVE_INTERVAL_TIME = 300;
    const SLEEP_INTERVAL_TIME = 5000;

    public static function create(
        TelegramMessageDTO $message,
        TelegramService $service,
        Config $config
    ): self;

    public static function clear(int $chatId): void;

    public static function getCommandByChatId(int $chatId): ?self;

    public function addMessage(TelegramMessageDTO $message): self;

    public function step(): self;

    public function getLastMessage(): TelegramMessageDTO;
}