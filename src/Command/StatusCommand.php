<?php

namespace Fastbolt\CommandScheduler\Command;

use Override;
use Symfony\Component\Console\Command\Command;

/**
 * @api
 */
abstract class StatusCommand extends Command implements StatusCommandInterface
{
    private const DEFAULT_ALARM_INTERVAL = 10;

    private string $statusText = '';

    private mixed $status = null;

    /**
     * @return mixed
     */
    #[Override]
    public function getStatus(): mixed
    {
        return $this->status;
    }

    /**
     * @return string
     */
    #[Override]
    public function getStatusText(): string
    {
        return $this->statusText;
    }

    /**
     * @return int
     */
    #[Override]
    public function getAlarmInterval(): int
    {
        return self::DEFAULT_ALARM_INTERVAL;
    }

    /**
     * @param string $statusText
     */
    public function setStatusText(string $statusText): void
    {
        $this->statusText = $statusText;
    }

    /**
     * @param mixed $status
     */
    public function setStatus(mixed $status): void
    {
        $this->status = $status;
    }
}
