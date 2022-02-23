<?php

namespace RB\System\Service\TelegramBots;

use RB\System\Exception\{HttpException, TelegramException};
use RB\System\Service\Http\{HttpService, Request, Response};

class TelegramService
{
    private const BASE_URL = 'https://api.telegram.org/bot%s/%s';
    private const TIME_OUT = 5;

    private string $token;
    private HttpService $httpService;

    public const COMMAND_GET_UPDATE = 'getUpdates';
    public const COMMAND_DELETE_MESSAGE = 'deleteMessage';
    public const COMMAND_SEND_MESSAGE = 'sendMessage';
    public const COMMAND_EDIT_MESSAGE_TEXT = 'editMessageText';
    public const COMMAND_SET_MY_COMMANDS = 'setMyCommands';

    public const COMMANDS = [
        self::COMMAND_GET_UPDATE,
        self::COMMAND_DELETE_MESSAGE,
        self::COMMAND_SEND_MESSAGE,
        self::COMMAND_EDIT_MESSAGE_TEXT,
        self::COMMAND_SET_MY_COMMANDS,
    ];

    public function __construct(string $token, HttpService $httpService)
    {
        $this->token = $token;
        $this->httpService = $httpService;
    }

    /**
     * @throws TelegramException
     * @throws HttpException
     */
    public function sendCommand(string $command, ?array $params = null): Response
    {
        if (!in_array($command, self::COMMANDS, true)) {
            throw new TelegramException('Invalid telegram command');
        }

        $url = sprintf(self::BASE_URL, $this->token, $command);
        if ($params) $url .= '?' . http_build_query($params);

        $request = (new Request())
            ->addHeaders('Content-Type', 'application/json')
            ->setMethod(Request::METHOD_GET)
            ->setUrl($url)
            ->setTimeOut(self::TIME_OUT);

        return $this->httpService->send($request);
    }
}