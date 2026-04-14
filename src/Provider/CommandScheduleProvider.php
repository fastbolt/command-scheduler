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
    ) {
    }

    /**
     * @return iterable<CommandSchedule>
     */
    public function getDueCommands(): iterable
    {
        $enabledSchedules = $this->commandScheduleRepository->findEnabledSchedules();
        $dueSchedules     = [];

        foreach ($enabledSchedules as $schedule) {
            $expression = $schedule->getCronExpression();
            if (!CronExpression::isValidExpression($expression)) {
                throw new InvalidExpressionException($schedule);
            }

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
