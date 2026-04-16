<?php

/**
 * Copyright © Fastbolt Schraubengroßhandels GmbH.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fastbolt\CommandScheduler\Exception;

use Fastbolt\CommandScheduler\Entity\CommandSchedule;
use Symfony\Component\Console\Exception\RuntimeException;

class InvalidExpressionException extends RuntimeException
{
    /**
     * @param CommandSchedule $schedule
     */
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
