<?php

namespace RB\System\Service;

use PHPMailer\PHPMailer\PHPMailer;
use RB\System\App\Config;

class MailService
{
    private static self $obj;

    private Config $config;

    private PHPMailer $mailer;

    private function __construct(?Config $config = null)
    {
        $this->config = $config ?: Config::create();
        $this->init();
    }

    public static function create(?Config $config = null): self
    {
        if (empty(self::$obj)) {
            self::$obj = new self($config);
        }

        return self::$obj;
    }

    private function init(): void
    {
        $config = $this->config->getValue('mail.connect');
        if (!$config) {
            throw new \Exception('Empty config mailer');
        }

        $this->mailer = new PHPMailer();
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->isSMTP();
        $this->mailer->SMTPAuth = true;
        $this->mailer->SMTPDebug = (int)$this->config->getEnv()->isDebug();
        $this->mailer->Host = $config['encryption'] . '://' . $config['host'];
        $this->mailer->Port = $config['port'];
        $this->mailer->Username = $config['username'];
        $this->mailer->Password = $config['password'];

        $configFrom = $this->config->getValue('mail.from');
        if ($configFrom) {
            $this->mailer->setFrom($configFrom['address'], $configFrom['name']);
        } else {
            $this->mailer->setFrom($config['username'], $this->config->getEnv()->getName());
        }
    }

    public function send(string $email, string $subject, string $bodyHtml, ?string $name): bool
    {
        $this->mailer->clearAddresses();
        $this->mailer->addAddress($email, $name);
        $this->mailer->Subject = $subject;
        $this->mailer->msgHTML($bodyHtml);

        return $this->mailer->send();
    }
}