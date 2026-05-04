<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Persistence;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Fastbolt\CommandScheduler\Entity\CommandLog;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Fastbolt\CommandScheduler\Repository\CommandScheduleRepository;
use Fastbolt\CommandScheduler\User\UserProvider;

final class CommandLogPersister
{
    /**
     * @param EntityManagerInterface    $entityManager
     * @param CommandScheduleRepository $commandScheduleRepository
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommandScheduleRepository $commandScheduleRepository,
        private readonly UserProvider $userProvider,
        private readonly SchemaManager $schemaManager
    ) {
    }

    /**
     * @param CommandSchedule $schedule
     *
     * @return CommandLog|null
     */
    public function createScheduleLog(CommandSchedule $schedule): ?CommandLog
    {
        if (!$this->schemaManager->logTableExists()) {
            return null;
        }

        $log = new CommandLog(
            $schedule->getCommand(),
            $schedule,
        );
        $log->setUserIdentifier($this->userProvider->getUserIdentifier());
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log;
    }

    /**
     * @param string $command
     *
     * @return CommandLog|null
     */
    public function createLog(string $command): ?CommandLog
    {
        // ensure jobs can be executed even though table does not exist
        if (!$this->schemaManager->logTableExists()) {
            return null;
        }

        $schedule = $this->getScheduleForCommand($command);
        $log      = new CommandLog(
            $command,
            $schedule,
        );
        $log->setStartedAt(new DateTimeImmutable());
        $log->setUserIdentifier($this->userProvider->getUserIdentifier());

        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $log;
    }

    /**
     * @param string $command
     *
     * @return CommandSchedule|null
     */
    private function getScheduleForCommand(string $command): ?CommandSchedule
    {
        // ensure jobs can be executed even though table does not exist
        if (!$this->schemaManager->scheduleTableExists()) {
            return null;
        }

        $items = $this->commandScheduleRepository->findBy(['command' => $command]);
        if (!$items || count($items) > 1) {
            return null;
        }

        return $items[0];
    }

    /**
     * @param CommandLog $log
     * @param int|null   $result
     *
     * @return void
     */

    public function finishLog(CommandLog $log, ?int $result): void
    {
        $log->setFinishedAt(new DateTimeImmutable());
        $log->setReturnCode($result);

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * @param CommandLog $log
     *
     * @return void
     */
    public function startLog(CommandLog $log): void
    {
        $log->setStartedAt(new DateTimeImmutable());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    /**
     * @param CommandLog $log
     * @param string     $status
     * @param string     $statusText
     *
     * @return void
     */
    public function updateStatus(CommandLog $log, string $status, string $statusText): void
    {
        $log->setStatusText($statusText);
        $log->setStatus($status);

        // set explicitly to ensure it is set even if statusText and status have not changed.
        $log->setChangedAt(new DateTimeImmutable());

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}
