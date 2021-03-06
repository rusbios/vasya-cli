<?php

namespace RB\System\Command;

use RB\System\Service\MailService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailSenderCommand extends Command
{
    protected static $defaultName = 'demon:mail-sender';
    protected static $defaultDescription = 'send mails from queue';

    private MailService $mailer;

    public function __construct(MailService $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $body = '<p><strong>«Hello, world!» </strong></p>';
            $isSend = $this->mailer->send('ryssia-@mail.ru', 'Тестовое письмо', $body, 'Руслан Р.М.');
            //TODO
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
