<?php

namespace Fastbolt\CommandScheduler\Provider;

use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Repository\CommandLogRepository;
use Symfony\Component\Console\Command\Command;

class CommandLogProvider
{
    public function __construct(
        private readonly CommandLogRepository $commandLogRepository,
    ) {
    }

    /**
     * Method for getting a list of all currently scheduled commands.
     * "Scheduled" are all commandLog entries where startedAt IS NULL, ordered by priority ascending.
     *
     * @return iterable<CommandLog>
     */
    public function getScheduledCommands(): iterable
    {
        return $this->commandLogRepository->findScheduledCommands();
    }

    /**
     * Method for getting a list of all currently running commands.
     * "Running" are all commandLog entries where startedAt IS NOT NULL and finishedAt is null, ordered by startedAt
     * ascending.
     *
     * @return iterable<CommandLog>
     */
    public function getRunningCommands(): iterable
    {
        return $this->commandLogRepository->findRunningCommands();
    }

    /**
     * @return iterable<CommandLog>
     */
    public function getErrors(): iterable
    {
        return $this->commandLogRepository->findErrorCommands();
    }

    public function hasLastExecutionFailed(string $command): bool
    {
        if (null === ($lastExecution = $this->commandLogRepository->getLastExecution($command))) {
            return false;
        }

        return $lastExecution->getReturnCode() !== Command::SUCCESS;
    }

    public function getScheduledCommandIdentifiers(): array
    {
        $result = array_filter(
            array_unique(
                array_map(
                    static fn(CommandLog $commandLog) => $commandLog->getCommandSchedule()?->getIdentifier() ?: null,
                    $this->getScheduledCommands()
                )
            )
        );

        return array_combine($result, $result);
    }

    public function getLastSuccess(string $command): ?CommandLog
    {
        return $this->commandLogRepository->getLastSuccess($command);
    }
}
