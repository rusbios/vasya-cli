<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\{Config, DBFactory};
use RB\System\App\DataBase\AllRepository;
use RB\System\App\DataBase\Model\UserModel;
use RB\System\Exception\CanselCommandException;
use RB\System\Helper\PasswordHelper;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

class LoginCommand extends AbstractCommand
{
    private ?int $answerMessageId = null;

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

        if ($this->user->getTelegramLogin() && $this->user->isAuth()) {
            $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Вы уже авторизированны',
            ])->getBody();
            throw new CanselCommandException();
        } elseif (empty($this->user->getTelegramLogin())) {
            $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Сначала необходимо зарегистрироваться /registration',
            ]);
            throw new CanselCommandException();
        }

        if (!$this->answerMessageId) {
            $this->answerMessageId = $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Введите пароль',
            ])->getBody()['result']['message_id'];

            return $this;
        }

        if (PasswordHelper::isVerify($message->getText(), $this->user->getPassword())) {
            $this->user->setIsAuth(true);
            $this->repository->save($this->user);
            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                'chat_id' => $message->getChat()['id'],
                'message_id' => $this->answerMessageId,
                'text' => 'Вы успешно авторизовались',
            ]);
            throw new CanselCommandException();
        }

        $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
            'chat_id' => $message->getChat()['id'],
            'message_id' => $this->answerMessageId,
            'text' => 'Пароль введён неверно, попробуйте снова',
        ]);
        return $this;
    }
}