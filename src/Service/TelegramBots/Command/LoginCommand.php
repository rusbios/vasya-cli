<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\Config;
use RB\System\Exception\CanselCommandException;
use RB\System\Service\DBFactory;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

class LoginCommand extends AbstractCommand
{
    private ?int $answerMessageId = null;

    private DBFactory $dbService;

    public function __construct(TelegramMessageDTO $message, TelegramService $service, Config $config)
    {
        parent::__construct($message, $service, $config);
        $this->dbService = DBFactory::create($config);
    }

    public function step(): CommandInterface
    {
        $message = $this->getLastMessage();
        $this->messages = [];

        $this->telegramService->sendCommand(TelegramService::COMMAND_DELETE_MESSAGE, [
            'chat_id' => $message->getChat()['id'],
            'message_id' => $message->getMessageId(),
        ]);

        if (!$this->answerMessageId) {
            $this->answerMessageId = $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Введите пароль',
            ])->getBody()['result']['message_id'];
            return $this;
        }

//        if ($message->getText() === 'QwertY') {
//            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
//                'chat_id' => $message->getChat()['id'],
//                'message_id' => $this->answerMessageId,
//                'text' => 'Успех, добро пожаловать в систему',
//            ]);
//            throw new CanselCommandException();
//        }

        $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
            'chat_id' => $message->getChat()['id'],
            'message_id' => $this->answerMessageId,
            'text' => 'Пароль неверный, попробуйте ещё раз',
        ]);

        return $this;
    }
}