<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\{Config, DataBase\Model\UserModel, DBFactory};
use RB\System\App\DataBase\AllRepository;
use RB\System\Exception\{CanselCommandException, DataBaseException};
use RB\System\Helper\PasswordHelper;
use RB\System\Service\TelegramBots\{AuthUserTrait, TelegramService};
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;

class RegistrationCommand extends AbstractCommand
{
    use AuthUserTrait;

    private ?int $answerMessageId = null;

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

        $this->getUser($message);

        if ($this->user->getTelegramLogin() && $this->user->isAuth()) {
            $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Вы уже авторизированны',
            ]);
            throw new CanselCommandException();
        }

        if (!$this->answerMessageId) {
            $this->answerMessageId = $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Придумайте пароль',
            ])->getBody()['result']['message_id'];

            $this->user
                ->setName(trim(join(' ', [
                    $message->getFrom()['first_name'],
                    $message->getFrom()['last_name'],
                ])))
                ->setTelegramChatId($message->getChat()['id'])
                ->setTelegramLogin($message->getFrom()['username']);

            return $this;
        }

        try {
            if (PasswordHelper::isValid($message->getText())) {
                $this->user->setPassword(PasswordHelper::getHash($message->getText()));
                $this->user->setIsAuth(true);
                $this->user = $this->repository->save($this->user);
            } else {
                $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                    'chat_id' => $message->getChat()['id'],
                    'message_id' => $this->answerMessageId,
                    'text' => 'Пароль не безопасный, придумайте более сложный пароль',
                ]);
                return $this;
            }

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