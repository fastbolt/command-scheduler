<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Persistence\CommandLogPersister;
use Fastbolt\CommandScheduler\Provider\CommandScheduleProvider;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'command-scheduler:schedule',
    description: 'Schedule commands for execution.',
)]
final class ScheduleCommandsCommand extends Command
{
    /**
     * @param CommandScheduleProvider $commandScheduleProvider
     * @param CommandLogPersister     $commandLogPersister
     */
    public function __construct(
        private readonly CommandScheduleProvider $commandScheduleProvider,
        private readonly CommandLogPersister $commandLogPersister,
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    #[Override]
    protected function configure(): void
    {
        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // find all enabled commands
        $commands = $this->commandScheduleProvider->getDueCommands();

        // create log entries for all commands to be executed
        foreach ($commands as $command) {
            $this->commandLogPersister->createScheduleLog($command);
        }

        $io->info(sprintf('Scheduled %d command(s) for execution.', count($commands)));

        return Command::SUCCESS;
    }
}
