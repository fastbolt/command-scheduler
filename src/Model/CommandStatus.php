<?php

namespace Fastbolt\CommandScheduler\Model;

use InvalidArgumentException;

class CommandStatus
{
    private int $numErrors = 0;

    private int $numSuccess = 0;

    private float $progress = 0.0;

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
}
