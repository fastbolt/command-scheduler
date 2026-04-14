<?php

namespace Fastbolt\CommandScheduler\Provider;

use Cron\CronExpression;
use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Fastbolt\CommandScheduler\Exception\InvalidExpressionException;
use Fastbolt\CommandScheduler\Repository\CommandScheduleRepository;

class CommandScheduleProvider
{
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

    public function getScheduledExecutions()
    {
    }
}
