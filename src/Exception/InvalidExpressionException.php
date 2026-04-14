<?php

namespace Fastbolt\CommandScheduler\Exception;

use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Symfony\Component\Console\Exception\RuntimeException;

class InvalidExpressionException extends RuntimeException
{
    public function __construct(
        private readonly CommandSchedule $schedule,
    ) {
        parent::__construct(
            sprintf(
                'Invalid cron expression "%s" in command "%s"',
                $schedule->getCronExpression(),
                $schedule->getCommand()
            )
        );
    }

    /**
     * @return CommandSchedule
     */
    public function getSchedule(): CommandSchedule
    {
        return $this->schedule;
    }
}
