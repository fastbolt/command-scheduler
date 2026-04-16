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
use Fastbolt\CommandScheduler\Repository\CommandScheduleRepository;

class CommandScheduleProvider
{
    /**
     * @param CommandScheduleRepository $commandScheduleRepository
     * @param CommandLogProvider        $commandLogProvider
     */
    public function __construct(
        private readonly CommandScheduleRepository $commandScheduleRepository,
        private readonly CommandLogProvider $commandLogProvider,
    ) {
    }

    /**
     * @return iterable<CommandSchedule>
     */
    public function getDueCommands(): iterable
    {
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
}
