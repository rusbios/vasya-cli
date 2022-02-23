<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\Config;
use RB\System\App\DataBase\AllRepository;
use RB\System\App\DBFactory;
use RB\System\Exception\CanselCommandException;
use RB\System\Service\TelegramBots\AuthUserTrait;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use RB\System\Service\TelegramBots\TelegramService;

class RunLocalCliCommand extends AbstractCommand
{
    use AuthUserTrait;

    private ?int $answerMessageId = null;

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
        if (!$user || !$user->isAuth() || !$user->isAdminRole()) {
            $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Недостаточно прав для выполнения этой команды',
            ])->getBody()['result']['message_id'];
            throw new CanselCommandException();
        }

        if (!$this->answerMessageId) {
            $this->answerMessageId = $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => 'Готов к выполнению команды',
            ])->getBody()['result']['message_id'];
            return $this;
        }

        if (preg_match('/^demon:(.*)$/', $message->getText(), $matches) > 0) {
            $command = $matches[1] . ' > /dev/null 2>&1 & echo $!;';
            $pid = exec($command);

            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                'chat_id' => $message->getChat()['id'],
                'message_id' => $this->answerMessageId,
                'text' => $matches[1] . "\nPID: " . $pid,
            ]);
        } else {
            $result = exec($message->getText());
            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                'chat_id' => $message->getChat()['id'],
                'message_id' => $this->answerMessageId,
                'text' => $message->getText() . "\n----------\n" . $result,
            ]);
        }

        throw new CanselCommandException();
    }
}