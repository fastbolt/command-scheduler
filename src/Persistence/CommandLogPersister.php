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
    ) {
    }

    /**
     * @param CommandSchedule $schedule
     *
     * @return CommandLog
     */
    public function createScheduleLog(CommandSchedule $schedule): CommandLog
    {
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
     * @return CommandLog
     */
    public function createLog(string $command): CommandLog
    {
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
}
