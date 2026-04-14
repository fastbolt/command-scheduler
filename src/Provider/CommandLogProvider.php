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
     * @return iterable<CommandLog>
     */
    public function getScheduledCommands(): iterable
    {
        return $this->commandLogRepository->findScheduledCommands();
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
