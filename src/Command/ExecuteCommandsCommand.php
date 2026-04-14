<?php

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Execution\CommandScheduleExecutor;
use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'command-scheduler:execute',
    description: 'Execute scheduled commands.',
)]
class ExecuteCommandsCommand extends Command
{
    public function __construct(
        private readonly CommandLogProvider $commandLogProvider,
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
        $io = new SymfonyStyle($input, $output);

        // I did not find any way to inject Application object using DIC, so dirty we go...
        $this->commandScheduleExecutor->setApplication($this->getApplication());

        // find scheduled commands
        $commands = $this->commandLogProvider->getScheduledCommands();

        // execute one by one
        foreach ($commands as $command) {
            $this->commandScheduleExecutor->execute($command, $io);
        }

        return Command::SUCCESS;
    }
}
