<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Provider;

use Cron\CronExpression;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Fastbolt\CommandScheduler\Exception\InvalidExpressionException;
use Fastbolt\CommandScheduler\Persistence\SchemaManager;
use Fastbolt\CommandScheduler\Repository\CommandScheduleRepository;

/**
 * @api
 */
class CommandScheduleProvider
{
    /**
     * @param CommandScheduleRepository $commandScheduleRepository
     * @param CommandLogProvider        $commandLogProvider
     */
    public function __construct(
        private readonly CommandScheduleRepository $commandScheduleRepository,
        private readonly CommandLogProvider $commandLogProvider,
        private readonly SchemaManager $schemaManager
    ) {
    }

    /**
     * @return CommandSchedule[]
     */
    public function getDueCommands(): array
    {
        if (!$this->schemaManager->scheduleTableExists()) {
            return [];
        }

        $alreadyScheduledCommands = $this->commandLogProvider->getScheduledCommandIdentifiers();
        $enabledSchedules         = $this->commandScheduleRepository->findEnabledSchedules();
        $dueSchedules             = [];

        foreach ($enabledSchedules as $schedule) {
            // Check if not-started schedule already exists
            $identifier = $schedule->getIdentifier();
            if (isset($alreadyScheduledCommands[$identifier])) {
                continue;
            }

            // Validate cron expression syntactically
            $expression = $schedule->getCronExpression();
            if (!CronExpression::isValidExpression($expression)) {
                throw new InvalidExpressionException($schedule);
            }

            // Check if cron expression is due
            $cronExpression = new CronExpression($expression);
            if (!$cronExpression->isDue()) {
                continue;
            }

            $dueSchedules[] = $schedule;
        }

        return $dueSchedules;
    }

    /**
     * @return CommandSchedule[]
     */
    public function getSchedules(): array
    {
        if (!$this->schemaManager->scheduleTableExists()) {
            return [];
        }

        $result = $this->commandScheduleRepository->findAll();
        usort(
            $result,
            static function (CommandSchedule $a, CommandSchedule $b): int {
                $nextRunA = $a->isEnabled() ? $a->getNextRun() : null;
                $nextRunB = $b->isEnabled() ? $b->getNextRun() : null;

                // items with null value should be bottom
                // items with date should be top, ordered by date asc (nearest on top)
                if (null === $nextRunA && null === $nextRunB) {
                    return 0;
                }
                if (null === $nextRunA) {
                    return 1;
                }
                if (null === $nextRunB) {
                    return -1;
                }

                return $nextRunA <=> $nextRunB;
            }
        );

        return $result;
    }

    /**
     * @return int
     */
    public function getNumSchedules(): int
    {
        if (!$this->schemaManager->scheduleTableExists()) {
            return 0;
        }

        return $this->commandScheduleRepository->count([]);
    }
}
