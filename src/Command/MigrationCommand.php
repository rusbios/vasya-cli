<?php

namespace RB\System\Command;

use RB\System\Service\DataBase\Migration;
use RB\System\Service\DBService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command
{
    protected static $defaultName = 'db:migration';
    protected static $defaultDescription = 'run actualize database';

    private Migration $migration;

    public function __construct(DBService $DBService)
    {
        $this->migration = new Migration($DBService->getConnection());
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            foreach ($this->migration->run() as  $item) {
                $output->writeln($item);
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
