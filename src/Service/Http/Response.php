<?php

namespace RB\System\Service\Http;

class Response
{
    private array $curlInfo;
    private ?array $body = null;

    public function __construct(array $curlInfo)
    {
        $this->curlInfo = $curlInfo;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function setBody(array $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function isOk(): bool
    {
        $code = $this->getHttpCode();
        return $code >= 200 && $code < 300;
    }

    public function getDuration(): int
    {
        return (int) $this->curlInfo['total_time'];
    }

    public function getHttpCode(): int
    {
        return (int) $this->curlInfo['http_code'];
    }
}
