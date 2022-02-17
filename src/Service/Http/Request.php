<?php

namespace RB\System\Service\Http;

class Request
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_DELETE = 'DELETE';

    private ?string $method = null;
    private ?string $url = null;
    private ?array $body = null;
    private ?int $timeOut = null;
    private array $headers = [];

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function setBody(?array $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getTimeOut(): ?int
    {
        return $this->timeOut;
    }

    public function setTimeOut(int $timeOut): self
    {
        $this->timeOut = $timeOut;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = [];
        foreach ($headers as $key => $value) {
            $this->addHeaders($key, $value);
        }
        return $this;
    }

    public function addHeaders(string $name, string $value): self
    {
        $this->headers[] = $name . ': ' . $value;
        return $this;
    }
}
