<?php

namespace RB\System\Service\TelegramBots\Models;

class MailDTO
{
    private ?string $emailTo = null;
    private ?string $nameTo = null;
    private ?string $subject = null;
    private ?string $body = null;

    public function getEmailTo(): ?string
    {
        return $this->emailTo;
    }

    public function setEmailTo(string $emailTo): self
    {
        $this->emailTo = $emailTo;
        return $this;
    }

    public function getNameTo(): ?string
    {
        return $this->nameTo;
    }

    public function setNameTo(string $nameTo): self
    {
        $this->nameTo = $nameTo;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function clear(): self
    {
        $this->emailTo = null;
        $this->nameTo = null;
        $this->subject = null;
        $this->body = null;
        return $this;
    }
}