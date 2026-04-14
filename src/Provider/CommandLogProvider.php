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
}
