<?php

namespace RB\System\Service\TelegramBots\Command;

use RB\System\App\Config;
use RB\System\Exception\CanselCommandException;
use RB\System\Service\MailService;
use RB\System\Service\TelegramBots\Models\{MailDTO, TelegramMessageDTO};
use RB\System\Service\TelegramBots\TelegramService;

class SendMailCommand extends AbstractCommand
{
    private MailService $mailService;

    private MailDTO $mail;

    private ?int $answerMessageId = null;

    private int $status = 0;

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

        $this->telegramService->sendCommand(TelegramService::COMMAND_DELETE_MESSAGE, [
            'chat_id' => $message->getChat()['id'],
            'message_id' => $message->getMessageId(),
        ]);

        if (!$this->answerMessageId) {
            $this->answerMessageId = $this->telegramService->sendCommand(TelegramService::COMMAND_SEND_MESSAGE, [
                'chat_id' => $message->getChat()['id'],
                'text' => "Сервис отправки электронных сообщений приветствует Вас.\nХотите сформировать сообщение?",
            ])->getBody()['result']['message_id'];
            $this->status = 1;
            return $this;
        }

        if ($this->isCanselCommand($message->getText())) {
            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                'chat_id' => $message->getChat()['id'],
                'message_id' => $this->answerMessageId,
                'text' => 'Сообщение отменено',
            ]);
            throw new CanselCommandException();
        }

        if ($this->status === 1) {
            if ($this->isSuccessAnswer($message->getText())) {
                $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                    'chat_id' => $message->getChat()['id'],
                    'message_id' => $this->answerMessageId,
                    'text' => "Новое сообщение создано.\nПолучатель:\nТема:\nСообщение:\n------------------\n\nНапишите email получателя.",
                ]);
                $this->status = 2;
                return $this;
            } else {
                $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                    'chat_id' => $message->getChat()['id'],
                    'message_id' => $this->answerMessageId,
                    'text' => 'Сообщение отменено',
                ]);
                throw new CanselCommandException();
            }
        }

        if ($this->status === 2) {
            // заполняем данные для отправки
            switch (true) {
                case empty($this->mail->getEmailTo()):
                    if (MailService::isValidEmail($message->getText())) {
                        $this->mail->setEmailTo($message->getText());
                    }
                    break;
                case empty($this->mail->getNameTo()):
                    $this->mail->setNameTo($message->getText());
                    break;

                case empty($this->mail->getSubject()):
                    $this->mail->setSubject($message->getText());
                    break;

                case empty($this->mail->getBody()):
                    $this->mail->setBody($message->getText());
                    break;
            }

            $text = sprintf(
                "Новое сообщение создано.\nПолучатель: %s\nТема: %s\nСообщение: %s\n------------------\n",
                join(' ', [$this->mail->getNameTo(), $this->mail->getEmailTo()]),
                $this->mail->getSubject() ?: '',
                $this->mail->getBody() ?: '',
            );

            switch (true) {
                case empty($this->mail->getEmailTo()):
                    $text .= 'Напишите email получателя.';
                    break;

                case empty($this->mail->getNameTo()):
                    $text .= 'Как зовут получателя?';
                    break;

                case empty($this->mail->getSubject()):
                    $text .= 'Темя письма?';
                    break;

                case empty($this->mail->getBody()):
                    $text .= 'Текст письма.';
                    break;

                default:
                    $text .= 'Отправить сообщение?';
                    $this->status = 3;
            }

            $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                'chat_id' => $message->getChat()['id'],
                'message_id' => $this->answerMessageId,
                'text' => $text,
            ]);
            return $this;
        }


        if ($this->status === 3) {
            if ($this->isSuccessAnswer($message->getText())) {
                $isSendMail = $this->mailService->send(
                    $this->mail->getEmailTo(),
                    $this->mail->getSubject(),
                    $this->mail->getBody(),
                    $this->mail->getNameTo()
                );
                $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                    'chat_id' => $message->getChat()['id'],
                    'message_id' => $this->answerMessageId,
                    'text' => ($isSendMail) ? 'Сообщение с темой "'.$this->mail->getSubject().'" отправленно' : 'Всё пошло не по плану',
                ]);
                throw new CanselCommandException();
            } else {
                $this->telegramService->sendCommand(TelegramService::COMMAND_EDIT_MESSAGE_TEXT, [
                    'chat_id' => $message->getChat()['id'],
                    'message_id' => $this->answerMessageId,
                    'text' => 'Сообщение с темой "'.$this->mail->getSubject().'" удалено',
                ]);
                throw new CanselCommandException();
            }
        }

        return $this;
    }

    private function isSuccessAnswer(string $text): bool
    {
        $text = mb_strtolower(trim($text));

        return ($text == 'да' || $text == 'д' || $text == 'y' || $text == 'yes');
    }
}
