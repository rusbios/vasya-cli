<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\Config;
use RB\System\Service\MailService;
use RB\System\Service\TelegramBots\Models\{MailDTO, TelegramMessageDTO};
use RB\System\Service\TelegramBots\TelegramService;

class SendMailCommand extends AbstractCommand
{
    private MailService $mailService;

    private MailDTO $mail;

    public function __construct(
        TelegramMessageDTO $message,
        TelegramService $telegramService,
        Config $config
    ) {
        $this->mailService = MailService::create($config);
        $this->mail = new MailDTO();
        parent::__construct($message, $telegramService, $config);
    }

    public function step(): self
    {
        $message = $this->getLastMessage();

        // заполняем данные для отправки
        switch ($message->getText() != '/send_mail') {
            case empty($this->mail->getEmailTo()):
                $this->mail->setEmailTo(trim($message->getText()));
                break;
            case empty($this->mail->getNameTo()):
                $this->mail->setNameTo(trim($message->getText()));
                break;

            case empty($this->mail->getSubject()):
                $this->mail->setSubject(trim($message->getText()));
                break;

            case empty($this->mail->getBody()):
                $this->mail->setBody('<p>' . trim($message->getText()) . '</p>');
                break;
        }

        switch (true) {
            case empty($this->mail->getEmailTo()):
                $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                    'chat_id' => $message->getChat()['id'],
                    'text' => 'Введите email получателя:',
                ]);
                break;

            case empty($this->mail->getNameTo()):
                $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                    'chat_id' => $message->getChat()['id'],
                    'text' => 'Как зовут получателя:',
                ]);
                break;

            case empty($this->mail->getSubject()):
                $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                    'chat_id' => $message->getChat()['id'],
                    'text' => 'Темя письма:',
                ]);
                break;

            case empty($this->mail->getBody()):
                $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                    'chat_id' => $message->getChat()['id'],
                    'text' => 'Текст письма:',
                ]);
                break;

            default:
                $isSendMail = $this->mailService->send(
                    $this->mail->getEmailTo(),
                    $this->mail->getSubject(),
                    $this->mail->getBody(),
                    $this->mail->getNameTo()
                );
                $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                    'chat_id' => $message->getChat()['id'],
                    'text' => ($isSendMail) ? 'Сообщение отправленно' : 'Всё пошло не по плану',
                ]);
                foreach ($this->messages as $mes) {
                    $this->telegramService->sendCommand(TelegramService::COMMAND_DELETE_MESSAGE, [
                        'chat_id' => $mes->getChat()['id'],
                        'message_id' => $mes->getMessageId(),
                    ]);
                }
        }

        return $this;
    }
}
