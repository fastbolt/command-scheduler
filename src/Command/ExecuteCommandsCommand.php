<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Execution\CommandScheduleExecutor;
use Fastbolt\CommandScheduler\Provider\CommandLogProvider;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'command-scheduler:execute',
    description: 'Execute scheduled commands.',
)]
final class ExecuteCommandsCommand extends Command
{
    /**
     * @param CommandLogProvider      $commandLogProvider
     * @param CommandScheduleExecutor $commandScheduleExecutor
     */
    public function __construct(
        private readonly CommandLogProvider $commandLogProvider,
        private readonly CommandScheduleExecutor $commandScheduleExecutor,
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
