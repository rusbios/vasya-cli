<?php

namespace RB\System\Service;

use RB\System\Exception\TelegramException;
use RB\System\Service\Http\{HttpService, Request, Response};

class TelegramService
{
    private const BASE_URL = 'https://api.telegram.org/bot%s/';
    private const TIME_OUT = 5;

    private string $token;
    private HttpService $httpService;

    public const COMMAND_GET_MESSAGE = 'getMessage';
    public const COMMANDS = [
        self::COMMAND_GET_MESSAGE => Request::METHOD_GET,
    ];

    public function __construct(string $token, HttpService $httpService)
    {
        $this->token = $token;
        $this->httpService = $httpService;
    }

    public function sendCommand(string $command, ?array $body = null): Response
    {
        $method = self::COMMANDS[$command];
        if (!$method) {
            throw new TelegramException('Invalid telegram command');
        }
        $request = (new Request())
            ->setMethod($method)
            ->setUrl(sprintf(self::BASE_URL, $this->token).$command)
            ->setTimeOut(self::TIME_OUT);

        if ($body) $request->setBody($body);

        return $this->httpService->send($request);
    }
}