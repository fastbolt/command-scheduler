<?php

namespace Fastbolt\CommandScheduler\Command;

use Fastbolt\CommandScheduler\Model\CommandStatus;
use Fastbolt\CommandScheduler\Model\CommandStatusInterface;
use Override;
use Symfony\Component\Console\Command\Command;

/**
 * @api
 */
abstract class StatusCommand extends Command implements StatusCommandInterface
{
    private const DEFAULT_ALARM_INTERVAL = 10;

    private string $statusText = '';

    private ?CommandStatusInterface $status = null;

    /**
     * @return CommandStatusInterface|null
     */
    #[Override]
    public function getStatus(): ?CommandStatusInterface
    {
        return $this->status;
    }

    /**
     * @param CommandStatusInterface|null $status
     */
    public function setStatus(?CommandStatusInterface $status): void
    {
        $this->status = $status;
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
     * @param string $statusText
     */
    public function setStatusText(string $statusText): void
    {
        $this->statusText = $statusText;
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
     * @param int $increment
     *
     * @return void
     */
    public function increaseError(int $increment = 1): void
    {
        if (null === $this->status) {
            $this->status = new CommandStatus();
        }
        $this->status->increaseError($increment);
    }

    /**
     * @param int $increment
     *
     * @return void
     */
    public function increaseSuccess(int $increment = 1): void
    {
        if (null === $this->status) {
            $this->status = new CommandStatus();
        }
        $this->status->increaseSuccess($increment);
    }

    /**
     * @param float $increment
     *
     * @return void
     */
    public function increaseProgress(float $increment): void
    {
        if (null === $this->status) {
            $this->status = new CommandStatus();
        }
        $this->status->increaseProgress($increment);
    }

    /**
     * @param int $progress
     *
     * @return void
     */
    public function setProgress(int $progress): void
    {
        if (null === $this->status) {
            $this->status = new CommandStatus();
        }
        $this->status->setProgress($progress);
    }
}
