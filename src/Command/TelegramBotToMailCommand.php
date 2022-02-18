<?php

namespace RB\System\Command;

use RB\System\App\Config;
use RB\System\Exception\TelegramException;
use RB\System\Service\TelegramBots\Command\AbstractCommand;
use RB\System\Service\TelegramBots\Command\CommandInterface;
use RB\System\Service\TelegramBots\TelegramService;
use RB\System\Service\TelegramBots\Models\TelegramMessageDTO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TelegramBotToMailCommand extends Command
{
    protected static $defaultName = 'demon:telegram-bot';
    protected static $defaultDescription = 'Telegram bot service';

    private TelegramService $baseService;
    private Config $config;
    private array $commands;

    public function __construct(TelegramService $baseService, Config $config) {
        $this->baseService = $baseService;
        $this->config = $config;
        $this->commands = $config->getValue('telegram.commands', []);
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setCommand();
        $updateId = -1;

        while (true) {
            $result = $this->baseService->sendCommand(TelegramService::COMMAND_GET_UPDATE, [
                'allowed_updates' => ["message"],
                'offset' => $updateId,
            ]);

            if (!$result->getBody()['ok']) {
                $output->writeln('telegram api error', self::FAILURE);
            }

            foreach ($result->getBody()['result'] as $item) {
                $message = new TelegramMessageDTO($item);
                $updateId = $message->getUpdateId();

                $command = $this->makeCommandService($message);
                if (!empty($command)) $command->step();
            }

            $updateId++;
            empty($command)
                ? usleep(CommandInterface::SLEEP_INTERVAL_TIME)
                : usleep(CommandInterface::ACTIVE_INTERVAL_TIME);
        }
    }

    private function makeCommandService(TelegramMessageDTO $messageDTO): ?CommandInterface
    {
        if (AbstractCommand::getCommandByChatId($messageDTO->getChat()['id'])) {
            return AbstractCommand::getCommandByChatId($messageDTO->getChat()['id']);
        }

        if (preg_match('/^\/(.*)$/', $messageDTO->getText(), $matches) === false) {
            return null;
        }

        if (!in_array($matches[1], array_keys($this->commands))) {
            return null;
            throw new TelegramException('command not found');
        }

        /** @var CommandInterface $class */
        $class = $this->commands[$matches[1]]['class'];
        return $class::create($messageDTO, $this->baseService, $this->config)
            ->addMessage($messageDTO);
    }

    private function setCommand(): void
    {
        $commands = [];
        foreach ($this->commands as $commandName => $options) {
            $commands[] = [
                'command' => '/'.$commandName,
                'description' => $options['description'],
            ];
        }
        $res = $this->baseService->sendCommand(TelegramService::COMMAND_SET_MY_COMMANDS, ['commands' => json_encode($commands)]);

        if (!$res->getBody()['ok']) {
            throw new TelegramException('Failed attempt to set commands');
        }
    }
}