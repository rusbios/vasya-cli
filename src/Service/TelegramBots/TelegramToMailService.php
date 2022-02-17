<?php

namespace RB\System\Service\TelegramBots;

use RB\System\App\Config;
use RB\System\Exception\TelegramException;
use RB\System\Service\Http\HttpService;
use RB\System\Service\MailService;
use RB\System\Service\TelegramBots\Models\MailDTO;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use Symfony\Component\Console\Output\OutputInterface;

class TelegramToMailService extends BaseTelegramService
{
    private const ACTIVE_INTERVAL_TIME = 1;
    private const SLEEP_INTERVAL_TIME = 5;

    private MailService $mailService;

    public function __construct(Config $config, HttpService $httpService)
    {
        $this->mailService = MailService::create($config);
        $token = $config->getValue('telegram.bots.mail.token');
        parent::__construct($token, $httpService);
    }

    public function listen(OutputInterface $output): void
    {
        $updateId = null;

        $cleanMessages = [];

        while (true) {
            $result = $this->sendCommand(self::COMMAND_GET_UPDATE, [
                'allowed_updates' => ["message"],
                'offset' => $updateId ?: -1,
            ]);

            if (!$result->getBody()['ok']) {
                throw new TelegramException('telegram api error');
            }

            if (count($result->getBody()['result']) === 0) {
                sleep(self::SLEEP_INTERVAL_TIME);
                continue;
            }

            foreach ($result->getBody()['result'] as $item) {
                $message = new TelegramMessageDTO($item);
                $updateId = $message->getUpdateId();

                if ($message->getText() == '/send_mail') {
                    $mail = new MailDTO();
                }

                if (isset($mail)) {
                    $cleanMessages[] = $message;

                    if ($message->getText() != '/send_mail') {
                        switch (true) {
                            case empty($mail->getEmailTo()):
                                $mail->setEmailTo(trim($message->getText()));
                                break;

                            case empty($mail->getNameTo()):
                                $mail->setNameTo(trim($message->getText()));
                                break;

                            case empty($mail->getSubject()):
                                $mail->setSubject(trim($message->getText()));
                                break;

                            case empty($mail->getBody()):
                                $mail->setBody('<p>' . trim($message->getText()) . '</p>');
                                break;
                        }
                    }

                    switch (true) {
                        case empty($mail->getEmailTo()):
                            $this->sendCommand(self::COMMAND_SEND_MESSAGE, [
                                'chat_id' => $message->getChat()['id'],
                                'text' => 'Введите email получателя:',
                            ]);
                            break;

                        case empty($mail->getNameTo()):
                            $this->sendCommand(self::COMMAND_SEND_MESSAGE, [
                                'chat_id' => $message->getChat()['id'],
                                'text' => 'Как зовут получателя:',
                            ]);
                            break;

                        case empty($mail->getSubject()):
                            $this->sendCommand(self::COMMAND_SEND_MESSAGE, [
                                'chat_id' => $message->getChat()['id'],
                                'text' => 'Темя письма:',
                            ]);
                            break;

                        case empty($mail->getBody()):
                            $this->sendCommand(self::COMMAND_SEND_MESSAGE, [
                                'chat_id' => $message->getChat()['id'],
                                'text' => 'Текст письма:',
                            ]);
                            break;

                        default:
                            $isSendMail = $this->mailService->send(
                                $mail->getEmailTo(),
                                $mail->getSubject(),
                                $mail->getBody(),
                                $mail->getNameTo()
                            );
                            $this->sendCommand(self::COMMAND_SEND_MESSAGE, [
                                'chat_id' => $message->getChat()['id'],
                                'text' => ($isSendMail) ? 'Сообщение отправленно' : 'Всё пошло не по плану',
                            ]);
                            foreach ($cleanMessages as $mes) {
                                $this->sendCommand(self::COMMAND_DELETE_MESSAGE, [
                                    'chat_id' => $mes->getChat()['id'],
                                    'message_id' => $mes->getMessageId(),
                                ]);
                            }
                            $cleanMessages = [];
                            unset($mail);
                    }
                }
            }
            $updateId++;

            usleep(self::ACTIVE_INTERVAL_TIME);
        }
    }
}
