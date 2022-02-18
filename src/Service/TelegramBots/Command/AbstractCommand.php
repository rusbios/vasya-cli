<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\Config;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

abstract class AbstractCommand implements CommandInterface
{
    /** @var self[] $activeCommands */
    protected static array $activeCommands = [];

    protected TelegramService $telegramService;

    protected Config $config;

    /** @var TelegramMessageDTO[] $messages */
    protected array $messages = [];

    public function __construct(TelegramMessageDTO $message, TelegramService $service, Config $config)
    {
        $this->telegramService = $service;
        $this->config = $config;
        $this->addMessage($message);
    }

    public static function create(TelegramMessageDTO $message, TelegramService $service, Config $config): CommandInterface
    {
        $chatId = $message->getChat()['id'];
        if (empty(self::$activeCommands[$chatId])) {
            self::$activeCommands[$chatId] = new static($message, $service, $config);
        }

        return self::$activeCommands[$chatId];
    }

    public static function clear(int $chatId): void
    {
        unset(self::$activeCommands[$chatId]);
    }

    public static function getCommandByChatId(int $chatId): ?self
    {
        if (empty(self::$activeCommands[$chatId])) {
            return null;
        }

        return self::$activeCommands[$chatId];
    }

    public function addMessage(TelegramMessageDTO $message): CommandInterface
    {
        $this->messages[$message->getMessageId()] = $message;
        return $this;
    }

    public function getLastMessage(): TelegramMessageDTO
    {
        return $this->messages[max(array_keys($this->messages))];
    }

    protected function isCommand(string $text): bool
    {
        return preg_match('/^\/(.*)$/', $text) > 0;
    }
}