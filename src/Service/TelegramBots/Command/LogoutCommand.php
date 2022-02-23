<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\{Config, DBFactory};
use RB\System\App\DataBase\AllRepository;
use RB\System\App\DataBase\Model\UserModel;
use RB\System\Exception\CanselCommandException;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

class LogoutCommand extends AbstractCommand
{
    private AllRepository $repository;

    private UserModel $user;

    public function __construct(TelegramMessageDTO $message, TelegramService $service, Config $config)
    {
        parent::__construct($message, $service, $config);
        $this->repository = new AllRepository(DBFactory::create($config)->getConnection());
        $this->user = new UserModel();
    }

    public function step(): CommandInterface
    {
        $message = $this->getLastMessage();
        $this->messages = [];

        $this->telegramService->sendCommand(TelegramService::COMMAND_DELETE_MESSAGE, [
            'chat_id' => $message->getChat()['id'],
            'message_id' => $message->getMessageId(),
        ]);

        // проверяем авторизацию
        $res = $this->repository->fetch($this->user::getTableName(), [
            'telegram_login' => $message->getFrom()['username'],
            'telegram_chat_id' => $message->getChat()['id'],
        ]);
        foreach ($res as $model) {
            $this->user = $model;
        }

        if ($this->user->isAuth()) {
            $this->user->setIsAuth(false);
            $this->repository->save($this->user);
        }

        $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
            'chat_id' => $message->getChat()['id'],
            'text' => 'Вы вышли из системы',
        ])->getBody()['result']['message_id'];
        throw new CanselCommandException();
    }
}