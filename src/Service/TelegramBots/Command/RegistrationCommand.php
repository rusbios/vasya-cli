<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\Config;
use RB\System\Exception\CanselCommandException;
use RB\System\Exception\DataBaseException;
use RB\System\Service\DBFactory;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

class RegistrationCommand extends AbstractCommand
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
                'text' => 'Придумайте пароль',
            ])->getBody()['result']['message_id'];
            return $this;
        }

        try {
            $this->dbService->getConnection()->insert('user', [
                'name' => trim(join(' ', [
                    $message->getFrom()['first_name'],
                    $message->getFrom()['last_name'],
                ])),
                'login' => $message->getFrom()['username'],
                'password' => $message->getText(),
            ]);

            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                'chat_id' => $message->getChat()['id'],
                'message_id' => $this->answerMessageId,
                'text' => 'Вы успешно зарегистрировались',
            ]);
            throw new CanselCommandException();
        } catch (DataBaseException $e) {
            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                'chat_id' => $message->getChat()['id'],
                'message_id' => $this->answerMessageId,
                'text' => 'Всё пошло не поплану',
            ]);
            throw new CanselCommandException();
        }
    }
}