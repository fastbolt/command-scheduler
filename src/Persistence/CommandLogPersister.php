<?php

namespace Fastbolt\CommandScheduler\Persistence;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Fastbolt\CommandScheduler\Repository\CommandScheduleRepository;

class CommandLogPersister
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommandScheduleRepository $commandScheduleRepository
    ) {
    }

    public function createScheduleLog(CommandSchedule $schedule): CommandLog
    {
        $log = new CommandLog(
            $schedule->getCommand(),
            $schedule,
        );
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log;
    }

    public function createLog(string $command): CommandLog
    {
        $schedule = $this->getScheduleForCommand($command);
        $log      = new CommandLog(
            $command,
            $schedule,
        );
        $log->setStartedAt(new DateTimeImmutable());

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

    public function startLog(CommandLog $log): void
    {
        $log->setStartedAt(new DateTimeImmutable());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    private function getScheduleForCommand(string $command): ?CommandSchedule
    {
        $items = $this->commandScheduleRepository->findBy(['command' => $command]);
        if (!$items || count($items) > 1) {
            return null;
        }

        return $items[0];
    }
}
