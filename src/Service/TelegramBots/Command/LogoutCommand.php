<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\{Config, DBFactory};
use RB\System\App\DataBase\AllRepository;
use RB\System\Exception\CanselCommandException;
use RB\System\Service\TelegramBots\AuthUserTrait;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

class LogoutCommand extends AbstractCommand
{
    use AuthUserTrait;

    public function __construct(TelegramMessageDTO $message, TelegramService $service, Config $config)
    {
        parent::__construct($message, $service, $config);
        $this->repository = new AllRepository(DBFactory::create($config)->getConnection());
    }

    public function step(): CommandInterface
    {
        $message = $this->getLastMessage();
        $this->messages = [];

        $this->telegramService->sendCommand(TelegramService::COMMAND_DELETE_MESSAGE, [
            'chat_id' => $message->getChat()['id'],
            'message_id' => $message->getMessageId(),
        ]);

        $user = $this->getUser($message);

        if ($user && $user->isAuth()) {
            $user->setIsAuth(false);
            $this->repository->save($user);
        }

        $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
            'chat_id' => $message->getChat()['id'],
            'text' => 'Вы вышли из системы',
        ])->getBody()['result']['message_id'];
        throw new CanselCommandException();
    }
}