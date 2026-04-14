<?php

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Execution\CommandScheduleExecutor;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'command-scheduler:execute',
    description: 'Execute scheduled commands.',
)]
class ExecuteCommandsCommand extends Command
{
    public function __construct(
        private readonly CommandScheduleProvider $commandScheduleProvider,
        private readonly CommandScheduleExecutor $commandScheduleExecutor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // I did not find any way to inject Application object using DIC, so dirty we go...
        $this->commandScheduleExecutor->setApplication($this->getApplication());

        // find all enabled commands
        // check if cron schedule is due
        // enhancement: add priority?
        // execute one by one
        // enhancement: emit event before and after execution
        // enhancement: allow to enrich command logs with implementation-specific details
        $commands = $this->commandScheduleProvider->getDueCommands();
        foreach ($commands as $command) {
            $this->commandScheduleExecutor->execute($command, $output);
        }

        return Command::SUCCESS;
    }
}
