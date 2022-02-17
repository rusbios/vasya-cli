<?php

namespace RB\System\Service\TelegramBots\Models;

class TelegramMessageDTO
{
    private int $updateId;
    private int $messageId;
    private array $from; //TODO
    private array $chat; //TODO
    private int $date;
    private string $text;
    private ?array $entities;

    public function __construct(array $data)
    {
        $this->updateId = $data['update_id'];
        $this->messageId = $data['message']['message_id'];
        $this->from = $data['message']['from'];
        $this->chat = $data['message']['chat'];
        $this->date = $data['message']['date'];
        $this->text = $data['message']['text'];
        $this->entities = $data['message']['entities'] ?? null;
    }

    public function getUpdateId(): int
    {
        return $this->updateId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getFrom(): array
    {
        return $this->from;
    }

    public function getChat(): array
    {
        return $this->chat;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getEntities(): ?array
    {
        return $this->entities;
    }
}
