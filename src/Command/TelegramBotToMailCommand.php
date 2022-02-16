<?php

namespace RB\System\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TelegramBotToMailCommand extends Command
{
    protected static $defaultName = 'demon:telegram-bot:mail';
    protected static $defaultDescription = 'Telegram bot service sent mail';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
