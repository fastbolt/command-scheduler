<?php

namespace Fastbolt\CommandScheduler\Persistence;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;

class CommandSchedulerLogPersister
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createLog(CommandSchedule $schedule): CommandLog
    {
        $log = new CommandLog(
            $schedule->getCommand(),
            $schedule,
        );
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log;
    }

    public function finishLog(CommandLog $log, int $result): void
    {
        $log->setFinishedAt(new DateTimeImmutable());
        $log->setReturnCode($result);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public function startLog(CommandLog $log):void
    {
        $log->setStartedAt(new DateTimeImmutable());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
