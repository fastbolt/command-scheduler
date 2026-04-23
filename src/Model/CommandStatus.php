<?php

namespace Fastbolt\CommandScheduler\Model;

use InvalidArgumentException;

/**
 * @api
 */
class CommandStatus implements CommandStatusInterface
{
    private int $numErrors = 0;

    private int $numSuccess = 0;

    private float $progress = 0.0;

    /**
     * @param int   $numErrors
     * @param int   $numSuccess
     * @param float $progress
     *
     * @return self
     */
    public static function create(int $numErrors, int $numSuccess, float $progress): self
    {
        $instance = new self();
        $instance->setNumErrors($numErrors);
        $instance->setNumSuccess($numSuccess);
        $instance->setProgress($progress);

        return $instance;
    }

    /**
     * @return int
     */
    public function getNumErrors(): int
    {
        return $this->numErrors;
    }

    /**
     * @param int $numErrors
     */
    public function setNumErrors(int $numErrors): void
    {
        $this->numErrors = $numErrors;
    }

    /**
     * @return int
     */
    public function getNumSuccess(): int
    {
        return $this->numSuccess;
    }

    /**
     * @param int $numSuccess
     */
    public function setNumSuccess(int $numSuccess): void
    {
        $this->numSuccess = $numSuccess;
    }

    /**
     * @return float
     */
    public function getProgress(): float
    {
        return $this->progress;
    }

    /**
     * @param float $progress
     */
    public function setProgress(float $progress): void
    {
        if ($progress > 100 || $progress < 0) {
            throw new InvalidArgumentException('Progress must be between 0 and 100');
        }

        $this->progress = $progress;
    }

    /**
     * @param int $increment
     *
     * @return void
     */
    public function increaseSuccess(int $increment = 1): void
    {
        $this->numSuccess += $increment;
    }

    /**
     * @param int $increment
     *
     * @return void
     */
    public function increaseError(int $increment = 1): void
    {
        $this->numErrors += $increment;
    }

    /**
     * @param float $increment
     *
     * @return void
     */
    public function increaseProgress(float $increment = 1): void
    {
        $this->progress += $increment;
    }
}
