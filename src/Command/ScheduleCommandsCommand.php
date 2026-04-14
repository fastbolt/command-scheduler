<?php

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Persistence\CommandSchedulerLogPersister;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        $io = new SymfonyStyle($input, $output);

        // find all enabled commands
        $commands = $this->commandScheduleProvider->getDueCommands();

        // create log entries for all commands to be executed
        foreach ($commands as $command) {
            $this->commandSchedulerLogPersister->createLog($command);
        }

        $io->info(sprintf('Scheduled %d command(s) for execution.', count($commands)));

        return Command::SUCCESS;
    }
}
