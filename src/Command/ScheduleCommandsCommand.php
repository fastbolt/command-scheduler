<?php

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Execution\CommandScheduleExecutor;
use Fastbolt\CommandScheduler\Persistence\CommandSchedulerLogPersister;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'command-scheduler:schedule',
    description: 'Schedule commands for execution.',
)]
class ScheduleCommandsCommand extends Command
{
    public function __construct(
        private readonly CommandScheduleProvider $commandScheduleProvider,
        private readonly CommandSchedulerLogPersister $commandSchedulerLogPersister,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // find all enabled commands
        $commands = $this->commandScheduleProvider->getDueCommands();


        // create log entries for all commands to be executed
        foreach ($commands as $command) {
            $this->commandSchedulerLogPersister->createLog($command);
        }

        return Command::SUCCESS;
    }
}
