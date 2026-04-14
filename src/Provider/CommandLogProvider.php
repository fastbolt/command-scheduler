<?php

namespace Fastbolt\CommandScheduler\Provider;

use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Repository\CommandLogRepository;

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
}
