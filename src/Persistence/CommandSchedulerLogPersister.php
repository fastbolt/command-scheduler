<?php

namespace Fastbolt\CommandScheduler\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;

class CommandSchedulerLogPersister
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function persistSchedule(CommandSchedule $schedule): CommandLog
    {
        $log = new CommandLog(
            $schedule->getCommand(),
            $schedule,
        );
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log;
    }
}
