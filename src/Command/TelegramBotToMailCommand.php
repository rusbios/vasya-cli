<?php

namespace RB\System\Command;

use RB\System\Exception\DemonException;
use RB\System\Service\TelegramBots\TelegramToMailService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TelegramBotToMailCommand extends Command
{
    protected static $defaultName = 'demon:telegram-bot:mail';
    protected static $defaultDescription = 'Telegram bot service sent mail';

    private TelegramToMailService $service;

    public function __construct(TelegramToMailService $service)
    {
        $this->service =$service;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->service->listen($output);
        } catch (\Exception $e) {
            throw new DemonException($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
